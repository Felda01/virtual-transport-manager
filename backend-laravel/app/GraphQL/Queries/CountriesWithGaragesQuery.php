<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Country;
use App\Models\Garage;
use App\Models\Location;
use App\Models\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class CountriesWithGaragesQuery extends Query
{
    protected $attributes = [
        'name' => 'countriesWithGarages',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('Country');
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

        $query = Country::query();

        /** @var User $user */
        $user = User::find($this->guard()->id());
        $locationTable = (new Location)->getTable();
        $garageTable = (new Garage)->getTable();

        $query = $query->whereIn('id', function ($q) use ($user, $locationTable, $garageTable) {
            $q->select('country_id')
                ->from($locationTable)
                ->rightjoin($garageTable, $garageTable . '.location_id', '=', $locationTable . '.id')
                ->where($garageTable . '.deleted_at', null)
                ->where('company_id', $user->company_id)
                ->get();
        });

        if ($args['limit'] === -1) {
            $args['limit'] = Country::count();
        }

        return $query->with($with)
            ->select($select)
            ->paginate($args['limit'], ['*'], 'page', $args['page']);
    }
}
