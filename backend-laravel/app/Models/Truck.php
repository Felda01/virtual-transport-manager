<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\Truck
 *
 * @property string $id
 * @property string $truck_model_id
 * @property string $company_id
 * @property string $trailer_id
 * @property string $garage_id
 * @property int $km
 * @property integer $status
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Company $company
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Driver[] $drivers
 * @property-read \App\Models\Garage $garage
 * @property-read \App\Models\Trailer|null $trailer
 * @property-read \App\Models\TruckModel $truckModel
 * @method static \Illuminate\Database\Eloquent\Builder|Truck newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Truck newQuery()
 * @method static \Illuminate\Database\Query\Builder|Truck onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Truck query()
 * @method static \Illuminate\Database\Eloquent\Builder|Truck whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Truck whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Truck whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Truck whereGarageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Truck whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Truck whereKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Truck whereTrailerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Truck whereTruckModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Truck whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Truck withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Truck withoutTrashed()
 * @mixin \Eloquent
 */
class Truck extends Model
{
    use HasFactory, HasUuid, SoftDeletes, LogsActivity;

    const MAX_DRIVERS_COUNT = 2;

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
        'truckModel',
        'status',
        'garage'
    ];

    /**
     * @param Activity $activity
     * @param string $eventName
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->description = "activity.{$eventName}.truck";
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function truckModel()
    {
        return $this->belongsTo(TruckModel::class)->withTrashed();
    }

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
    public function trailer()
    {
        return $this->belongsTo(Trailer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function garage()
    {
        return $this->belongsTo(Garage::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchTruckModel($query, $value)
    {
        $brands = explode(',', $value);

        if ($brands && count($brands) > 0) {
            return $query->whereHas('truckModel', function (Builder $query) use ($brands) {
                $query->whereIn('id', $brands);
            });
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
    public function searchOnlyWithDriver($query, $value)
    {
        if ($value) {
            return $query->whereHas('drivers');
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchOnlyWithTrailer($query, $value)
    {
        if ($value) {
            return $query->whereHas('trailer');
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
