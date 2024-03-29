<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Utilities\FilterUtility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
 * @property string $price
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
 * @method static \Illuminate\Database\Eloquent\Builder|GarageModel wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GarageModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GarageModel extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that are searchable.
     *
     * @var string[]
     */
    public static $searchable = [
        'trailer_count',
        'truck_count',
        'price',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function garages()
    {
        return $this->hasMany(Garage::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchTrailerCount($query, $value)
    {
        $values = FilterUtility::getRangeValues($value);

        if (array_key_exists('min', $values)) {
            $query = $query->where('trailer_count', '>=', $values['min']);
        }
        if (array_key_exists('max', $values)) {
            $query = $query->where('trailer_count', '<=', $values['max']);
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchTruckCount($query, $value)
    {
        $values = FilterUtility::getRangeValues($value);

        if (array_key_exists('min', $values)) {
            $query = $query->where('truck_count', '>=', $values['min']);
        }
        if (array_key_exists('max', $values)) {
            $query = $query->where('truck_count', '<=', $values['max']);
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchPrice($query, $value)
    {
        $values = FilterUtility::getRangeValues($value);

        if (array_key_exists('min', $values)) {
            $query = $query->where('price', '>=', $values['min']);
        }
        if (array_key_exists('max', $values)) {
            $query = $query->where('price', '<=', $values['max']);
        }

        return $query;
    }
}
