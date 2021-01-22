<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Utilities\FilterUtility;
use App\Utilities\StatusUtility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\Driver
 *
 * @property string $id
 * @property string $first_name
 * @property string $last_name
 * @property int $gender
 * @property int $adr
 * @property integer $status
 * @property string $image
 * @property string $company_id
 * @property string $truck_id
 * @property string $location_id
 * @property string $garage_id
 * @property int $salary
 * @property int $satisfaction
 * @property string $preferred_road_trips
 * @property \Illuminate\Support\Carbon|null $last_in_garage_at
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Company $company
 * @property-read \App\Models\Garage $garage
 * @property-read \App\Models\Location $location
 * @property-read \App\Models\Truck|null $truck
 * @method static \Illuminate\Database\Eloquent\Builder|Driver newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Driver newQuery()
 * @method static \Illuminate\Database\Query\Builder|Driver onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Driver query()
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereGarageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereAdr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereLastInGarageAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver wherePreferredRoadTrips($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereSatisfaction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereTruckId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Driver withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Driver withoutTrashed()
 * @mixin \Eloquent
 */
class Driver extends Model
{
    use HasFactory, HasUuid, SoftDeletes, LogsActivity;

    const GENDER_MALE = 0;
    const GENDER_FEMALE = 1;

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
        'salary',
        'adr',
        'preferred_road_trips',
        'garage',
        'status'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'last_in_garage_at' => 'datetime'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
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
    public function garage()
    {
        return $this->belongsTo(Garage::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return Builder|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activeOrder()
    {
        return $this->orders()->whereHas('roadTrip', function (Builder $query) {
            $query->where('status', '!=', StatusUtility::FINISHED);
        });
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
            return  $query->whereIn('adr', $adrs);
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchSalary($query, $value)
    {
        $values = FilterUtility::getRangeValues($value);

        if (array_key_exists('min', $values)) {
            $query = $query->where('salary', '>=', $values['min']);
        }
        if (array_key_exists('max', $values)) {
            $query = $query->where('salary', '<=', $values['max']);
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchPreferredRoadTrips($query, $value)
    {
        $preferredRoadTrips = explode(',', $value);

        if ($preferredRoadTrips && count($preferredRoadTrips) > 0) {
            return  $query->whereIn('preferred_road_trips', $preferredRoadTrips);
        }

        return $query;
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
            return $query->whereIn('status', $statuses);
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchGarage($query, $value)
    {
        $garages = explode(',', $value);

        if ($garages && count($garages) > 0) {
            return $query->whereHas('garage', function (Builder $query) use ($garages) {
                $query->whereIn('id', $garages);
            });
        }

        return $query;
    }
}
