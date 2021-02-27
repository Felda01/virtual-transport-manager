<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Events\RefreshQuery;
use App\Models\Company;
use App\Models\User;
use App\Rules\CanDeleteUserRule;
use App\Rules\ModelFromCompanyRule;
use App\Utilities\BroadcastUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class DeleteUserMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteUser',
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

    public function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                'string',
                'exists:users,id,deleted_at,NULL',
                new ModelFromCompanyRule('User'),
                new CanDeleteUserRule(),
            ]
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
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var User $user */
        $user = User::find($args['id']);

        $deletedUser = $user;

        try {
            $user->delete();
        } catch (\Exception $e) {
        }

        if (!$user->trashed()) {
            throw new \GraphQL\Error\Error(trans('validation.general_exception'));
        }
        BroadcastUtility::broadcast(new RefreshQuery(Company::currentCompany(), 'User', $deletedUser->id));
        return $deletedUser;
    }
}
