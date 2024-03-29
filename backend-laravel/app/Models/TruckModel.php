<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Utilities\FilterUtility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\TruckModel
 *
 * @property string $id
 * @property string $name
 * @property string $brand
 * @property int $engine_power
 * @property string $chassis
 * @property int $load
 * @property string $price
 * @property int $emission_class
 * @property int $km
 * @property string $insurance
 * @property string $tax
 * @property string $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TruckModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TruckModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TruckModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|TruckModel whereBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TruckModel whereChassis($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TruckModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TruckModel whereEmissionClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TruckModel whereEnginePower($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TruckModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TruckModel whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TruckModel whereInsurance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TruckModel whereKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TruckModel whereLoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TruckModel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TruckModel wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TruckModel whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TruckModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TruckModel extends Model
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
        'brand',
        'engine_power',
        'chassis',
        'load',
        'price',
        'emission_class',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trucks()
    {
        return $this->hasMany(Truck::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchBrand($query, $value)
    {
        $brands = explode(',', $value);

        if ($brands && count($brands) > 0) {
            return $query->whereIn('brand', $brands);
        }
        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchChassis($query, $value)
    {
        $chassis = explode(',', $value);

        if ($chassis && count($chassis) > 0) {
            return $query->whereIn('chassis', $chassis);
        }
        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchEmissionClass($query, $value)
    {
        $emissionClasses = explode(',', $value);

        if ($emissionClasses && count($emissionClasses) > 0) {
            return $query->whereIn('emission_class', $emissionClasses);
        }
        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchEnginePower($query, $value)
    {
        $values = FilterUtility::getRangeValues($value);

        if (array_key_exists('min', $values)) {
            $query = $query->where('engine_power', '>=', $values['min']);
        }
        if (array_key_exists('max', $values)) {
            $query = $query->where('engine_power', '<=', $values['max']);
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchLoad($query, $value)
    {
        $values = FilterUtility::getRangeValues($value);

        if (array_key_exists('min', $values)) {
            $query = $query->where('load', '>=', $values['min']);
        }
        if (array_key_exists('max', $values)) {
            $query = $query->where('load', '<=', $values['max']);
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
