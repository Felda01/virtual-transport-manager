<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\TrailerModel;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class TrailerModelsQuery extends Query
{
    protected $attributes = [
        'name' => 'trailerModels',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('TrailerModel');
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        return $this->guard()->check() ? $this->guard()->user()->hasRole('admin') : false;
    }

    public function getAuthorizationMessage(): string
    {
        return 'You are not authorized to perform this action';
    }

    public function args(): array
    {
        return [
            'limit' => [
                'type' => Type::int(),
                'defaultValue' => 50,
            ],
            'page' => [
                'type' => Type::int(),
                'defaultValue' => 1,
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var SelectFields $fields */
        $fields = $getSelectFields();
        $select = $fields->getSelect();
        $with = $fields->getRelations();

        if ($args['limit'] === -1) {
            $args['limit'] = TrailerModel::count();
        }

        return TrailerModel::with($with)
            ->select($select)
            ->orderBy('price')
            ->paginate($args['limit'], ['*'], 'page', $args['page']);
    }
}
