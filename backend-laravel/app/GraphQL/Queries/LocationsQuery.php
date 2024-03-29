<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Location;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class LocationsQuery extends Query
{
    protected $attributes = [
        'name' => 'locations',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('Location');
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        return $this->guard()->check();
    }

    public function getAuthorizationMessage(): string
    {
        return trans('validation.unauthorized');
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
            $args['limit'] = Location::count();
        }

        return Location::with($with)
            ->select($select)
            ->orderBy('name->' . app()->getLocale())
            ->paginate($args['limit'], ['*'], 'page', $args['page']);
    }
}
