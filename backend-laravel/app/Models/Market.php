<?php

namespace App\Models;

use App\Traits\HasUuid;
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'expires_at' => 'datetime',
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
}
