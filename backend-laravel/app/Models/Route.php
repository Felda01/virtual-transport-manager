<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Route
 *
 * @property string $id
 * @property string $location1_id
 * @property string $location2_id
 * @property int $time
 * @property int $distance
 * @property float $fee
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Location $location1
 * @property-read \App\Models\Location $location2
 * @method static \Illuminate\Database\Eloquent\Builder|Route newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Route newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Route query()
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereLocation1Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereLocation2Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Route extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location1()
    {
        return $this->belongsTo(Location::class, 'location1_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location2()
    {
        return $this->belongsTo(Location::class, 'location2_id');
    }

    /**
     * Return distance between 2 locations
     *
     * @param $locationFrom Location
     * @param $locationTo Location
     * @return float|int
     */
    public static function getDistanceBetween($locationFrom, $locationTo)
    {
        if ($locationFrom->id == $locationTo->id) {
            return 0;
        }

        $R = 6371;
        $dLat = deg2rad($locationTo->lat - $locationFrom->lat);
        $dLon = deg2rad($locationTo->lng - $locationFrom->lng);
        $a = (sin($dLat/2) * sin($dLat/2)) + cos(deg2rad($locationFrom->lat)) * cos(deg2rad($locationTo->lat)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $d = $R * $c;

        return ceil($d);
    }

    /**
     * @param $location1 string
     * @param $location2 string
     * @return string[]
     */
    public static function getSortedLocations($location1, $location2)
    {
        if ($location1 < $location2) {
            return [$location1, $location2];
        } else {
            return [$location2, $location1];
        }
    }
}
