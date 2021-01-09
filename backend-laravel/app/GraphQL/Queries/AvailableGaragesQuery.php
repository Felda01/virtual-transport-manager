<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Driver;
use App\Models\Garage;
use App\Models\GarageModel;
use App\Models\Trailer;
use App\Models\Truck;
use App\Models\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class AvailableGaragesQuery extends Query
{
    protected $attributes = [
        'name' => 'availableGarages',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('Garage');
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
            'type' => [
                'type' => Type::string()
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var SelectFields $fields */
        $fields = $getSelectFields();
        $select = $fields->getSelect();
        $with = $fields->getRelations();

        $query = Garage::query();

        /** @var User $user */
        $user = User::find($this->guard()->id());

        $table = null;
        $column = null;

        $garageTable = (new Garage)->getTable();
        $garageModelTable = (new GarageModel)->getTable();

        if ($args['type'] === 'truck') {
            $table = (new Truck)->getTable();
            $column = 'truck_count';
        } else if ($args['type'] === 'trailer') {
            $table = (new Trailer)->getTable();
            $column = 'trailer_count';
        } else if ($args['type'] === 'driver') {
            $table = (new Driver())->getTable();
            $column = 'truck_count';
        }

        if ($table) {
            $query = $query->whereIn('id', function ($q) use ($user, $table, $column, $garageTable, $garageModelTable) {
                $q->select($garageTable . '.id')
                    ->from($garageTable)
                    ->leftjoin($garageModelTable, $garageTable . '.garage_model_id', '=', $garageModelTable . '.id')
                    ->leftjoin($table, $table . '.garage_id', '=', $garageTable . '.id')
                    ->where($garageTable . '.company_id', $user->company_id)
                    ->groupBy($garageTable . '.id', $garageModelTable . '.' . $column)
                    ->havingRaw('COUNT(DISTINCT `' . $table . '`.`id`) < `' . $garageModelTable . '`.`' . $column . '`')
                    ->get();
            });
        }

        if ($args['limit'] === -1) {
            $args['limit'] = Garage::count();
        }

        return $query->with($with)
            ->select($select)
            ->paginate($args['limit'], ['*'], 'page', $args['page']);

    }
}
