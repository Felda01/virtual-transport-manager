<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\GarageModel;
use App\Rules\NotExistsRelationRule;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class DeleteGarageModelMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteGarageModel',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('GarageModel');
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
                'exists:garage_models',
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
        /** @var GarageModel $garageModel */
        $garageModel = GarageModel::find($args['id']);

        $deletedGarageModel = $garageModel;

        try {
            $garageModel->delete();
        } catch (\Exception $e) {
        }

        return $deletedGarageModel;
    }
}
