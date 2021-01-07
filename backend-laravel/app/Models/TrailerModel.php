<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Utilities\FilterUtility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\TrailerModel
 *
 * @property string $id
 * @property string $name
 * @property string $type
 * @property int $load
 * @property int $adr
 * @property int $km
 * @property string $insurance
 * @property string $tax
 * @property string $image
 * @property string $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Cargo[] $cargos
 * @property-read int|null $cargos_count
 * @method static \Illuminate\Database\Eloquent\Builder|TrailerModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrailerModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrailerModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|TrailerModel whereAdr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrailerModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrailerModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrailerModel whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrailerModel whereInsurance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrailerModel whereKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrailerModel whereLoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrailerModel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrailerModel whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrailerModel whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrailerModel wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrailerModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TrailerModel extends Model
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
        'type',
        'load',
        'adr',
        'price',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cargos()
    {
        return $this->belongsToMany(Cargo::class, 'cargo_trailer_model');
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
    public function searchType($query, $value)
    {
        $types = explode(',', $value);

        if ($types && count($types) > 0) {
            return $query->whereIn('type', $types);
        }
        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchAdr($query, $value)
    {
        $adr = explode(',', $value);

        if ($adr && count($adr) > 0) {
            return $query->whereIn('adr', $adr);
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
