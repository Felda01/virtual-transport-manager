<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Order
 *
 * @property string $id
 * @property string $market_id
 * @property string $driver1_id
 * @property string $driver2_id
 * @property string $truck_id
 * @property string $trailer_id
 * @property string $road_trip_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Driver $driver1
 * @property-read \App\Models\Driver $driver2
 * @property-read \App\Models\Market $market
 * @property-read \App\Models\RoadTrip $roadTrip
 * @property-read \App\Models\Trailer $trailer
 * @property-read \App\Models\Truck $truck
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDriver1Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDriver2Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereMarketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRoadTripId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTrailerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTruckId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    use HasFactory, HasUuid;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function market()
    {
        return $this->belongsTo(Market::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function driver1()
    {
        return $this->belongsTo(Driver::class, 'driver1');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function driver2()
    {
        return $this->belongsTo(Driver::class, 'driver2');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function trailer()
    {
        return $this->belongsTo(Trailer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function roadTrip()
    {
        return $this->belongsTo(RoadTrip::class);
    }

}
