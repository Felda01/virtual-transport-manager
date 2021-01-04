<?php
namespace App\Utilities;


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
}
