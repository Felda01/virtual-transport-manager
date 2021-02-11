<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Role;
use App\Models\User;
use App\Rules\ExistsAllRule;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class CreateUserMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createUser',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('User');
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        // true, if logged in
        return $this->guard()->check() && $this->guard()->user()->hasPermissionTo(\App\Models\Permission::MANAGE_PERSONS, \App\Models\Permission::GUARD);
    }

    public function getAuthorizationMessage(): string
    {
        return trans('validation.unauthorized');
    }

    public function rules(array $args = []): array
    {
        return [
            'first_name' => [
                'required',
                'string'
            ],
            'last_name' => [
                'required',
                'string'
            ],
            'email' => [
                'required',
                'string',
                'email',
                'unique:users,email,NULL,id,deleted_at,NULL'
            ],
            'roles' => [
                'required',
                'string',
                new ExistsAllRule('Role')
            ],
        ];
    }

    public function args(): array
    {
        return [
            'first_name' => [
                'name' => 'first_name',
                'type' => Type::nonNull(Type::string()),
            ],
            'last_name' => [
                'name' => 'last_name',
                'type' => Type::nonNull(Type::string()),
            ],
            'email' => [
                'name' => 'email',
                'type' => Type::nonNull(Type::string()),
            ],
            'roles' => [
                'name' => 'roles',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $result = DB::transaction(function () use($args) {
            /** @var User $currentUser */
            $currentUser = User::current();

            /** @var User $user */
            $user = User::create([
                'first_name' => $args['first_name'],
                'last_name' => $args['last_name'],
                'email' => $args['email'],
                'image' => '',
                'password' => Hash::make(Str::random(60)),
                'salary' => 0,
                'company_id' => $currentUser->company_id
            ]);

            $roles = Role::whereIn('id', explode(',', $args['roles']))->get();

            $user->syncRoles(...$roles);
            $userSaved = $user->save();

            $status = Password::sendResetLink(
                ['email' => $args['email']]
            );

            if (!$userSaved) {
                throw new \GraphQL\Error\Error(trans('validation.general_exception'));
            }

            return [
                'status' => $status,
                'user' => $user
            ];
        });

        if (Password::RESET_LINK_SENT === $result['status']) {
            return $result['user'];
        } else {
            try {
                $result['user']->delete();
            } catch (\GraphQL\Error\Error $e) {
            } finally {
                return response()->json([
                    'errors' => [
                        'email' => trans($result['status'])
                    ]
                ], 422);
            }
        }
    }
}
