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

    /**
     * @param $locationFrom
     * @param $locationTo
     * @return array[]
     */
    public static function getPaths($locationFrom, $locationTo)
    {
//        $response = Http::withBasicAuth(config('services.nodejs.user'), config('services.nodejs.password'))->post(config('services.nodejs.url'), [
//            'from' => $locationFrom,
//            'to' => $locationTo,
//        ]);
//
//        if (!$response->successful()) {
//            return [];
//        }
//
//        return $response->json();

        return ["result" => [["path" => ["6eb19875-7dba-4407-9605-3dabf4972c63","e05b0a43-0cfb-4bf6-80be-e2718b68e712","05932cb4-ec20-4745-9d01-0a4918d0a12a"],"cost" => 4404],["path" => ["6eb19875-7dba-4407-9605-3dabf4972c63","e05b0a43-0cfb-4bf6-80be-e2718b68e712","023eb1af-7df5-49e5-aed4-aab7548a183c","05932cb4-ec20-4745-9d01-0a4918d0a12a"],"cost" => 4678], ["cost" => 9007199254740991]]];
    }

    /**
     * @param $time
     * @return float|int
     */
    public static function calculateRoadTripTime($time)
    {
        $time = $time + self::calculateDelay();

        $days = ceil($time / (config('constants.working_hours') * 60));

        return $days * 24 * 60;
    }

    /**
     * @return int
     */
    private static function calculateDelay()
    {
        try {
            if (random_int(0, 10) > 7) {
                return random_int(120, 360);
            }
        } catch (\Exception $e) {
        }
        return 0;
    }

    /**
     * @return int
     */
    public static function calculateDamage()
    {
        try {
            if (random_int(0, 10) > 7) {
                return random_int(1, 15);
            }
        } catch (\Exception $e) {
        }
        return 0;
    }
}
