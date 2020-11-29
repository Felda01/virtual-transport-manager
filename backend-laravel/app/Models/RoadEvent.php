<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\RoadEvent
 *
 * @property string $id
 * @property string $type
 * @property int $delay
 * @property string $end
 * @property string $location_from_id
 * @property string $location_to_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Location $locationFrom
 * @property-read \App\Models\Location $locationTo
 * @method static \Illuminate\Database\Eloquent\Builder|RoadEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoadEvent newQuery()
 * @method static \Illuminate\Database\Query\Builder|RoadEvent onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RoadEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder|RoadEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoadEvent whereDelay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoadEvent whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoadEvent whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoadEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoadEvent whereLocationFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoadEvent whereLocationToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoadEvent whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoadEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|RoadEvent withTrashed()
 * @method static \Illuminate\Database\Query\Builder|RoadEvent withoutTrashed()
 * @mixin \Eloquent
 */
class RoadEvent extends Model
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
}
