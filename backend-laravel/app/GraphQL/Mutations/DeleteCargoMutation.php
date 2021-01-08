<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Cargo;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class DeleteCargoMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteCargo',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('Cargo');
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        // true, if logged in
        return $this->guard()->check() && $this->guard()->user()->hasPermissionTo(\App\Models\Permission::MANAGE_STATIC, \App\Models\Permission::GUARD);
    }

    public function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                'string',
                'exists:cargos,id,deleted_at,NULL',
            ]
        ];
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
        /** @var Cargo $cargo */
        $cargo = Cargo::find($args['id']);

        $deletedCargo = $cargo;

        try {
            $cargo->delete();
        } catch (\Exception $e) {
        }

        return $deletedCargo;
    }
}
