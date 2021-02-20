<?php
namespace App\Utilities;

use App\Models\Route;

/**
 * Class PathUtility
 * @package App\Utilities
 */
class PathUtility
{
    /**
     * @param $paths
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRoutesFromPath($paths)
    {
         $query = Route::query();

         for ($i = 0; $i < count($paths) - 1; $i++) {
             $locations = Route::getSortedLocations($paths[$i], $paths[$i + 1]);

             if ($i === 0) {
                 $query = $query->where(function($query) use ($locations) {
                     $query->where('location1_id', $locations[0])
                         ->where('location2_id', $locations[1]);
                 });
             } else {
                $query = $query->orWhere(function($query) use ($locations) {
                    $query->where('location1_id', $locations[0])
                        ->where('location2_id', $locations[1]);
                });
             }
         }

         return $query->get();
    }
}
