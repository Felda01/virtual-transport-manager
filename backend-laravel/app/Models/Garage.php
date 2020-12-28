<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
