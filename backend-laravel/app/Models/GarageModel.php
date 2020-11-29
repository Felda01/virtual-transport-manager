<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GarageModel
 *
 * @property string $id
 * @property string $name
 * @property int $truck_count
 * @property int $trailer_count
 * @property string $location_id
 * @property string $insurance
 * @property string $tax
 * @property string $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|GarageModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GarageModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GarageModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|GarageModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GarageModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GarageModel whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GarageModel whereInsurance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GarageModel whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GarageModel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GarageModel whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GarageModel whereTrailerCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GarageModel whereTruckCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GarageModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GarageModel extends Model
{
    use HasFactory, HasUuid;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
