<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RoadTrip
 *
 * @property string $id
 * @property int $km
 * @property int $time
 * @property float $fees
 * @property int $damage
 * @property array $routes
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read RoadTrip|null $order
 * @method static \Illuminate\Database\Eloquent\Builder|RoadTrip newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoadTrip newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoadTrip query()
 * @method static \Illuminate\Database\Eloquent\Builder|RoadTrip whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoadTrip whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoadTrip whereKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoadTrip whereRoutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoadTrip whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoadTrip whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoadTrip whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RoadTrip extends Model
{
    use HasFactory, HasUuid;

    const SHORT_KM = 500;
    const MEDIUM_KM = 1500;
    const SHORT = 0;
    const MEDIUM = 1;
    const LONG = 2;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'routes' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function order()
    {
        return $this->hasOne(RoadTrip::class);
    }

    /**
     * @return int
     */
    public function getType()
    {
        if ($this->km <= self::SHORT_KM) {
            return self::SHORT;
        }
        if ($this->km <= self::MEDIUM_KM) {
            return self::MEDIUM;
        }
        return self::LONG;
    }
}
