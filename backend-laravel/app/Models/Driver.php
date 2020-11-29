<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Driver
 *
 * @property string $id
 * @property string $first_name
 * @property string $last_name
 * @property int $gender
 * @property string $status
 * @property string $image
 * @property string $company_id
 * @property string $truck_id
 * @property string $location_id
 * @property string $garage_id
 * @property int $salary
 * @property int $satisfaction
 * @property string $preferred_road_trips
 * @property int $days_on_road
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
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereDaysOnRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereGarageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Driver whereImage($value)
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
}
