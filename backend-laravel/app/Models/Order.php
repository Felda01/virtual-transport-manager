<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Utilities\FilterUtility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

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
 * @property string $company_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Driver $driver1
 * @property-read \App\Models\Driver $driver2
 * @property-read \App\Models\Market $market
 * @property-read \App\Models\RoadTrip $roadTrip
 * @property-read \App\Models\Trailer $trailer
 * @property-read \App\Models\Truck $truck
 * @property-read \App\Models\Company $company
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
    use HasFactory, HasUuid, LogsActivity, SoftDeletes;

    const LAST_ORDERS_COUNT = 5;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    protected static $logUnguarded = true;

    /**
     * @var bool
     */
    protected static $logOnlyDirty = true;

    /**
     * The attributes that are searchable.
     *
     * @var string[]
     */
    public static $searchable = [
        'status',
        'price',
        'km',
        'damage'
    ];

    /**
     * @param Activity $activity
     * @param string $eventName
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->description = "activity.{$eventName}.order";
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function market()
    {
        return $this->belongsTo(Market::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function drivers()
    {
        return $this->belongsToMany(Driver::class, 'driver_order');
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchStatus($query, $value)
    {
        $statuses = explode(',', $value);

        if ($statuses && count($statuses) > 0) {
            return $query = $query->whereHas('roadTrip', function (Builder $query) use ($statuses) {
                $query->whereIn('status', $statuses);
            });
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchPrice($query, $value)
    {
        $values = FilterUtility::getRangeValues($value);

        if (array_key_exists('min', $values)) {
            $query = $query->whereHas('market', function (Builder $query) use ($values) {
                $query->where('price', '>=', $values['min']);
            });
        }
        if (array_key_exists('max', $values)) {
            $query =$query = $query->whereHas('market', function (Builder $query) use ($values) {
                $query->where('price', '<=', $values['max']);
            });
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchKm($query, $value)
    {
        $values = FilterUtility::getRangeValues($value);

        if (array_key_exists('min', $values)) {
            $query = $query->whereHas('roadTrip', function (Builder $query) use ($values) {
                $query->where('km', '>=', $values['min']);
            });
        }
        if (array_key_exists('max', $values)) {
            $query =$query = $query->whereHas('roadTrip', function (Builder $query) use ($values) {
                $query->where('km', '<=', $values['max']);
            });
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchDamage($query, $value)
    {
        $values = FilterUtility::getRangeValues($value);

        if (array_key_exists('min', $values)) {
            $query = $query->whereHas('roadTrip', function (Builder $query) use ($values) {
                $query->where('damage', '>=', $values['min']);
            });
        }
        if (array_key_exists('max', $values)) {
            $query =$query = $query->whereHas('roadTrip', function (Builder $query) use ($values) {
                $query->where('damage', '<=', $values['max']);
            });
        }

        return $query;
    }
}
