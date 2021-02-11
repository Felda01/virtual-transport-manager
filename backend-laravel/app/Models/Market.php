<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Utilities\FilterUtility;
use App\Utilities\StatusUtility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Market
 *
 * @property string $id
 * @property string $location_from
 * @property string $location_to
 * @property string $cargo_id
 * @property string $customer_from
 * @property string $customer_to
 * @property string $company_id
 * @property string $price
 * @property string $frequency
 * @property int $amount
 * @property int $km
 * @property int $count_of_repetitions
 * @property \Illuminate\Support\Carbon $start
 * @property \Illuminate\Support\Carbon $end
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cargo $cargo
 * @property-read \App\Models\Customer $customerFrom
 * @property-read \App\Models\Customer $customerTo
 * @property-read \App\Models\Location $locationFrom
 * @property-read \App\Models\Location $locationTo
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @property-read int|null $orders_count
 * @method static \Illuminate\Database\Eloquent\Builder|Market newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Market newQuery()
 * @method static \Illuminate\Database\Query\Builder|Market onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Market query()
 * @method static \Illuminate\Database\Eloquent\Builder|Market whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Market whereCargoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Market whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Market whereCountOfRepetitions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Market whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Market whereCustomerFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Market whereCustomerTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Market whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Market whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Market whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Market whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Market whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Market whereLocationFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Market whereLocationTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Market wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Market whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Market whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Market withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Market withoutTrashed()
 * @mixin \Eloquent
 */
class Market extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * The attributes that are searchable.
     *
     * @var string[]
     */
    public static $searchable = [
        'adr',
        'weight',
        'engine_power',
        'chassis',
        'trailers',
        'driver'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function locationFrom()
    {
        return $this->belongsTo(Location::class, 'location_from');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function locationTo()
    {
        return $this->belongsTo(Location::class, 'location_to');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cargo()
    {
        return $this->belongsTo(Cargo::class)->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customerFrom()
    {
        return $this->belongsTo(Customer::class, 'customer_from');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customerTo()
    {
        return $this->belongsTo(Customer::class, 'customer_to');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchAdr($query, $value)
    {
        $adrs = explode(',', $value);

        if ($adrs && count($adrs) > 0) {
            return $query->whereHas('cargo', function (Builder $query) use ($adrs) {
                $query->whereIn('adr', $adrs);
            });
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchWeight($query, $value)
    {
        $values = FilterUtility::getRangeValues($value);

        if (array_key_exists('min', $values)) {
            $query = $query->whereHas('cargo', function (Builder $query) use ($values) {
                $query->where('weight', '>=', $values['min']);
            });
        }
        if (array_key_exists('max', $values)) {
            $query = $query->whereHas('cargo', function (Builder $query) use ($values) {
                $query->where('weight', '<=', $values['max']);
            });
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchEnginePower($query, $value)
    {
        $values = FilterUtility::getRangeValues($value);

        if (array_key_exists('min', $values)) {
            $query = $query->whereHas('cargo', function (Builder $query) use ($values) {
                $query->where('engine_power', '>=', $values['min']);
            });
        }
        if (array_key_exists('max', $values)) {
            $query =$query = $query->whereHas('cargo', function (Builder $query) use ($values) {
                $query->where('engine_power', '<=', $values['max']);
            });
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchChassis($query, $value)
    {
        $chassis = explode(',', $value);

        if ($chassis && count($chassis) > 0) {
            return $query = $query->whereHas('cargo', function (Builder $query) use ($chassis) {
                $query->whereIn('chassis', $chassis);
            });
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchTrailers($query, $value)
    {
        $trailers = explode(',', $value);

        if ($trailers && count($trailers) > 0) {
            return $query = $query->whereHas('cargo', function (Builder $query) use ($trailers) {
                $query->whereHas('trailerModels', function (Builder $query) use ($trailers) {
                    $query->whereIn('id', $trailers);
                });

            });
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchDriver($query, $value)
    {
        /** @var Driver $driver */
        $driver = Driver::with('truck.trailer.trailerModel', 'truck.truckModel')
            ->where('id', $value)
            ->where('company_id', auth()->guard('api')->user()->company_id)
            ->whereIn('status', [StatusUtility::READY, StatusUtility::SLEEP])
            ->first();

        if ($driver) {
            $allChassis = config('constants.truck_chassis');
            $chassis = array_slice($allChassis, 0, array_search($driver->truck->truckModel->chassis, $allChassis) + 1);
            $query = $this->searchChassis($query, implode(',', $chassis));
            $query = $this->searchEnginePower($query, '0_' . $driver->truck->truckModel->engine_power);
            $query = $this->searchWeight($query, '0_' . $driver->truck->truckModel->load);
            $query = $this->searchWeight($query, '0_' . $driver->truck->trailer->trailerModel->load);
            $query = $this->searchTrailers($query, $driver->truck->trailer->trailerModel->id);

            $query = $this->searchAdr($query, implode(',', range(0, $driver->truck->trailer->trailerModel->adr)));
            $query = $this->searchAdr($query, implode(',', range(0, $driver->adr)));
        }
        return $query;
    }
}
