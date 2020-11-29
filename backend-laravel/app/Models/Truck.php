<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Truck
 *
 * @property string $id
 * @property string $truck_model_id
 * @property string $company_id
 * @property string $trailer_id
 * @property string $garage_id
 * @property int $km
 * @property \Illuminate\Support\Carbon $next_service
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Company $company
 * @property-read \App\Models\Driver $driver
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
 * @method static \Illuminate\Database\Eloquent\Builder|Truck whereNextService($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Truck whereTrailerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Truck whereTruckModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Truck whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Truck withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Truck withoutTrashed()
 * @mixin \Eloquent
 */
class Truck extends Model
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
        'next_service' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function truckModel()
    {
        return $this->belongsTo(TruckModel::class);
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
    public function trailer()
    {
        return $this->hasOne(Trailer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function garage()
    {
        return $this->belongsTo(Garage::class);
    }
}
