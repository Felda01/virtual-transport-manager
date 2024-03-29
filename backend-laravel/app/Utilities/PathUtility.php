<?php
namespace App\Utilities;

use App\Models\Location;
use App\Models\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

/**
 * Class PathUtility
 * @package App\Utilities
 */
class PathUtility
{
    /**
     * @param $edges
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRoutesFromPath($edges)
    {
         $query = Route::query();

         for ($i = 0; $i < count($edges); $i++) {
             $edge = $edges[$i];
             $locations = Route::getSortedLocations($edge['fromNode'], $edge['toNode']);

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
     * @param $edges
     * @return array
     */
    public static function getLocationsFromPath($edges)
    {
        $result = [];
        $count = count($edges);
        for ($i = 0; $i < $count; $i++) {
            $edge = $edges[$i];
            if ($i == $count - 1) {
                $result[] = $edge['fromNode'];
                $result[] = $edge['toNode'];
            } else {
                $result[] = $edge['fromNode'];
            }
        }

        return $result;
    }

    /**
     * @param $locationFrom
     * @param $locationTo
     * @return array[]
     */
    public static function getPaths($locationFrom, $locationTo)
    {
        if (App::environment('local')) {
            return ["result" => [["totalCost" => 4404,"edges"=>[["fromNode"=>"6eb19875-7dba-4407-9605-3dabf4972c63","toNode"=>"e05b0a43-0cfb-4bf6-80be-e2718b68e712","weight"=>123],["fromNode"=>"e05b0a43-0cfb-4bf6-80be-e2718b68e712","toNode"=>"05932cb4-ec20-4745-9d01-0a4918d0a12a","weight"=>4281]]],["totalCost"=>4678,"edges"=>[["fromNode"=>"6eb19875-7dba-4407-9605-3dabf4972c63","toNode"=>"e05b0a43-0cfb-4bf6-80be-e2718b68e712","weight"=>123],["fromNode"=>"e05b0a43-0cfb-4bf6-80be-e2718b68e712","toNode"=>"023eb1af-7df5-49e5-aed4-aab7548a183c","weight"=>186],["fromNode"=>"023eb1af-7df5-49e5-aed4-aab7548a183c","toNode"=>"05932cb4-ec20-4745-9d01-0a4918d0a12a","weight"=>4369]]]]];
        } else {
            $response = Http::withBasicAuth(config('services.nodejs.user'), config('services.nodejs.password'))->post(config('services.nodejs.url'), [
                'from' => $locationFrom,
                'to' => $locationTo,
            ]);

            if (!$response->successful()) {
                return [];
            }

            return $response->json();
        }
    }

    /**
     * @param $time
     * @param $driversCount
     * @return float|int
     */
    public static function calculateRoadTripTime($time, $driversCount = 1)
    {
        $time = $time + self::calculateDelay();

        $days = ceil($time / (config('constants.working_hours') * $driversCount * 60));

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

    /**
     * @param $path
     * @param string[] $columns
     * @return Location[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getPathLocations($path, $columns = ['id', 'lat', 'lng'])
    {
        $query = Location::query();

        $query = $query->whereIn('id', $path);

        foreach ($path as $item) {
                $query = $query->orderByRaw('id = "'. $item .'"');
        }

        return $query->get($columns);
    }
}
