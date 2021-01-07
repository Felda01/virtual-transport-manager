<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Garage;
use App\Models\Location;
use App\Models\Role;
use App\Models\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class AvailableLocationsQuery extends Query
{
    protected $attributes = [
        'name' => 'availableLocations',
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

        $query = Location::query();

        /** @var User $user */
        $user = User::find($this->guard()->id());

        if (!$user->hasRole([Role::ADMIN])) {
            $query = $query->whereNotIn('id', function ($q) use ($user) {
                $q->select('location_id')
                    ->from((new Garage)->getTable())
                    ->where('company_id', $user->company_id)
                    ->get();
            });
        }

        if ($args['limit'] === -1) {
            $args['limit'] = Location::count();
        }

        return $query->with($with)
            ->select($select)
            ->orderBy('name')
            ->paginate($args['limit'], ['*'], 'page', $args['page']);
    }
}
