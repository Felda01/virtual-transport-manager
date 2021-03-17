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
 * App\Models\Garage
 *
 * @property string $id
 * @property string $location_id
 * @property string $garage_model_id
 * @property string $company_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Company $company
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Driver[] $drivers
 * @property-read int|null $drivers_count
 * @property-read \App\Models\GarageModel $garageModel
 * @property-read \App\Models\Location $location
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Trailer[] $trailers
 * @property-read int|null $trailers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Truck[] $trucks
 * @property-read int|null $trucks_count
 * @method static \Illuminate\Database\Eloquent\Builder|Garage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Garage newQuery()
 * @method static \Illuminate\Database\Query\Builder|Garage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Garage query()
 * @method static \Illuminate\Database\Eloquent\Builder|Garage whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Garage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Garage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Garage whereGarageModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Garage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Garage whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Garage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Garage withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Garage withoutTrashed()
 * @mixin \Eloquent
 */
class Garage extends Model
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
        'garage_model',
        'country',
        'available_truck_spot',
        'available_trailer_spot',
    ];

    /**
     * @param Activity $activity
     * @param string $eventName
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->description = "activity.{$eventName}.garage";
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
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function garageModel()
    {
        return $this->belongsTo(GarageModel::class)->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trucks()
    {
        return $this->hasMany(Truck::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trailers()
    {
        return $this->hasMany(Trailer::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchGarageModel($query, $value)
    {
        $garageModels = explode(',', $value);

        if ($garageModels && count($garageModels) > 0) {
            return $query->whereHas('garageModel', function (Builder $query) use ($garageModels) {
                $query->whereIn('id', $garageModels);
            });
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchCountry($query, $value)
    {
        $countries = explode(',', $value);

        if ($countries && count($countries) > 0) {
            return $query->whereHas('location', function (Builder $query) use ($countries) {
                $query->whereHas('country', function (Builder $query) use ($countries) {
                    $query->whereIn('id', $countries);
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
    public function searchAvailableTruckSpot($query, $value)
    {
        $user = User::current();
        $table = (new Truck)->getTable();
        $column = 'truck_count';
        $garageTable = (new Garage)->getTable();
        $garageModelTable = (new GarageModel)->getTable();

        if ($value) {
            return $query->whereIn('id', function ($q) use ($user, $table, $column, $garageTable, $garageModelTable) {
                return self::freeSpotQuery($q, $user, $table, $column, $garageTable, $garageModelTable)->get();
            });
        }
        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchAvailableTrailerSpot($query, $value)
    {
        $user = User::current();
        $table = (new Truck)->getTable();
        $column = 'trailer_count';
        $garageTable = (new Garage)->getTable();
        $garageModelTable = (new GarageModel)->getTable();

        if ($value) {
            return $query->whereIn('id', function ($q) use ($user, $table, $column, $garageTable, $garageModelTable) {
                return self::freeSpotQuery($q, $user, $table, $column, $garageTable, $garageModelTable)->get();
            });
        }
        return $query;
    }

    /**
     * @param Builder $query
     * @param $user
     * @param $table
     * @param $column
     * @param $garageTable
     * @param $garageModelTable
     * @return Builder
     */
    public static function freeSpotQuery($query, $user, $table, $column, $garageTable, $garageModelTable)
    {
        return $query->select($garageTable . '.id')
            ->from($garageTable)
            ->leftjoin($garageModelTable, $garageTable . '.garage_model_id', '=', $garageModelTable . '.id')
            ->leftjoin($table, $table . '.garage_id', '=', $garageTable . '.id')
            ->where($garageTable . '.company_id', $user->company_id)
            ->groupBy($garageTable . '.id', $garageModelTable . '.' . $column)
            ->havingRaw('COUNT(DISTINCT `' . $table . '`.`id`) < `' . $garageModelTable . '`.`' . $column . '` * ' . ($table === (new Driver())->getTable() ? 2 : 1));
    }
}
