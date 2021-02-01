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
 * App\Models\Trailer
 *
 * @property string $id
 * @property string $trailer_model_id
 * @property string $company_id
 * @property string $garage_id
 * @property int $km
 * @property integer $status
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Company $company
 * @property-read \App\Models\Garage $garage
 * @property-read \App\Models\TrailerModel $trailerModel
 * @property-read \App\Models\Truck $truck
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer newQuery()
 * @method static \Illuminate\Database\Query\Builder|Trailer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer whereGarageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer whereKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer whereTrailerModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Trailer withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Trailer withoutTrashed()
 * @mixin \Eloquent
 */
class Trailer extends Model
{
    use HasFactory, HasUuid, SoftDeletes, LogsActivity;

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
        'trailerModel',
        'status',
        'garage'
    ];

    /**
     * @param Activity $activity
     * @param string $eventName
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->description = "activity.{$eventName}.trailer";
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function trailerModel()
    {
        return $this->belongsTo(TrailerModel::class)->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function truck()
    {
        return $this->hasOne(Truck::class);
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
    public function searchTrailerModel($query, $value)
    {
        $brands = explode(',', $value);

        if ($brands && count($brands) > 0) {
            return $query->whereHas('trailerModel', function (Builder $query) use ($brands) {
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
