<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
 * @method static \Illuminate\Database\Eloquent\Builder|TrailerModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TrailerModel extends Model
{
    use HasFactory, HasUuid;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cargos()
    {
        return $this->belongsToMany(Cargo::class, 'cargo_trailer_model');
    }
}
