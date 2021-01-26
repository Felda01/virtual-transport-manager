<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Trailer;
use App\Rules\ModelFromCompanyRule;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class AvailableTrailersInGarageQuery extends Query
{
    protected $attributes = [
        'name' => 'availableTrailersInGarage',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('Trailer');
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        // true, if logged in
        return $this->guard()->check();
    }

    public function getAuthorizationMessage(): string
    {
        return trans('validation.unauthorized');
    }

    public function args(): array
    {
        return [
            'garage' => [
                'required',
                'string',
                'exists:garages,id,deleted_at,NULL',
                new ModelFromCompanyRule('Garage'),
            ],
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

        $query = Trailer::query();

        $query = $query->whereHas('garage', function(Builder $query) use ($args) {
            $query->where('id', $args['garage']);
        })->whereDoesntHave('truck');

        if ($args['limit'] === -1) {
            $args['limit'] = Trailer::count();
        }

        return $query->with($with)
            ->select($select)
            ->paginate($args['limit'], ['*'], 'page', $args['page']);
    }
}