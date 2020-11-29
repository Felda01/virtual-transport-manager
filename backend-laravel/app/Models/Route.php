<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Route
 *
 * @property string $id
 * @property string $location1_id
 * @property string $location2_id
 * @property int $time
 * @property int $distance
 * @property string $fee
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Location $location1
 * @property-read \App\Models\Location $location2
 * @method static \Illuminate\Database\Eloquent\Builder|Route newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Route newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Route query()
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereLocation1Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereLocation2Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Route extends Model
{
    use HasFactory, HasUuid;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location1()
    {
        return $this->belongsTo(Location::class, 'location1_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location2()
    {
        return $this->belongsTo(Location::class, 'location2_id');
    }
}
