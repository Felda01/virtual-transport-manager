<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Garage;
use App\Models\GarageModel;
use App\Utilities\CompanyUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class AvailableGarageModelUpgradesQuery extends Query
{
    protected $attributes = [
        'name' => 'availableGarageUpgrades',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('GarageModel');
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        $model = Garage::find($args['id']);

        return $this->guard()->check() && CompanyUtility::can($model, $this->guard()->id());
    }

    public function getAuthorizationMessage(): string
    {
        return trans('validation.unauthorized');
    }

    public function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                'string',
                'exists:garages,id,deleted_at,NULL'
            ],
        ];
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::string(),
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

        /** @var Garage $currentGarage */
        $currentGarage = Garage::find($args['id']);

        $currentGarageModel = $currentGarage->garageModel;

        $query = GarageModel::query();

        if ($args['limit'] === -1) {
            $args['limit'] = GarageModel::count();
        }

        $query->where('truck_count', '>=', $currentGarageModel->truck_count)
            ->where('trailer_count', '>=', $currentGarageModel->trailer_count)
            ->where('id', '!=', $currentGarageModel->id);

        return $query->with($with)
            ->select($select)
            ->paginate($args['limit'], ['*'], 'page', $args['page']);
    }
}
