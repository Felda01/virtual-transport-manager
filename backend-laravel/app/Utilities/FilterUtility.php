<?php
namespace App\Utilities;


use App\Models\User;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Class FilterUtility
 * @package App\Utilities
 */
class FilterUtility
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $model
     * @param $searchableFields
     * @param $filters
     * @return \Illuminate\Support\Collection
     */
    public static function handleFilter($query, $model,  $searchableFields, $filters)
    {
        $filterCollection = collect($filters);
        $searchableFieldsCollection = collect($searchableFields);

        $filteredCollection = $filterCollection->filter(function ($value, $key) use ($searchableFieldsCollection) {
            return $searchableFieldsCollection->contains($value['column']);
        });

        foreach ($filteredCollection->toArray() as $item) {
            $method = 'search' . Str::camel($item['column']);
            if (method_exists($model, $method)) {
                $query = call_user_func_array([$model, $method], [$query, $item['value']]);
            }
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $table
     * @param $sortValue
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function handleSort($query, $table, $sortValue)
    {
        $result = explode('_', $sortValue);

        if (count($result) !== 2 || !in_array($result[1], ['desc', 'asc']) || !Schema::hasColumn($table, $result[0])) {
            return $query;
        }

        return $query->orderBy($result[0], $result[1]);
    }

    /**
     * @param $value
     * @return array
     */
    public static function getRangeValues($value)
    {
        $result = [];

        $values = explode('_', $value);

        if (count($values) !== 2) {
            return $result;
        }

        if (is_numeric($values[0])) {
            $result['min'] = $values[0];
        }
        if (is_numeric($values[1])) {
            $result['max'] = $values[1];
        }

        return $result;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function filterCompany($query, $user)
    {
        return $query->where('company_id', $user->company_id);
    }
}
