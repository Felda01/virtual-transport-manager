<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Events\RefreshQuery;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Rules\ExistsAllRule;
use App\Rules\ExistsCompanyOwnerRule;
use App\Utilities\BroadcastUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateUserSalaryMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateUserSalary',
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
        return $this->guard()->check() && $this->guard()->user()->hasPermissionTo(\App\Models\Permission::MANAGE_SALARY, \App\Models\Permission::GUARD);
    }

    public function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                'string',
                'exists:users,id,deleted_at,NULL',
            ],
            'salary' => [
                'required',
                'min:0'
            ],
            'roles' => [
                'required',
                'string',
                new ExistsAllRule('Role'),
                new ExistsCompanyOwnerRule($args['id']),
            ],
        ];
    }

    public function getAuthorizationMessage(): string
    {
        return trans('validation.unauthorized');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::string()),
            ],
            'salary' => [
                'name' => 'salary',
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
        /** @var User $user */
        $user = User::find($args['id']);
        $user->salary = $args['salary'];

        $roles = Role::whereIn('id', explode(',', $args['roles']))->get();

        $user->syncRoles(...$roles);
        $user->save();

        BroadcastUtility::broadcast(new RefreshQuery(Company::currentCompany(), 'User', $user->id));

        return $user;
    }
}
