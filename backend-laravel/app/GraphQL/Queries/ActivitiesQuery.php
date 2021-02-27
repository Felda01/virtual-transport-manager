<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;
use Spatie\Activitylog\Models\Activity;

class ActivitiesQuery extends Query
{
    protected $attributes = [
        'name' => 'activities',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('Activity');
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
            'user' => [
                'type' => Type::string(),
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var SelectFields $fields */
        $fields = $getSelectFields();
        $select = $fields->getSelect();
        $with = $fields->getRelations();

        $query = Activity::query();

        if ($args['user'] && User::where('id', $args['user'])->exists()) {
            $query = $query->whereHasMorph('causer', [User::class], function ($query) use($args) {
                $query->where('id', $args['user']);
            });
        }

        return $query->with($with)
            ->select($select)
            ->orderBy('created_at', 'desc')
            ->paginate($args['limit'], ['*'], 'page', $args['page']);
    }
}
