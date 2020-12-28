<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\Cargo
 *
 * @property string $id
 * @property string $name
 * @property int $adr
 * @property int $weight
 * @property int $engine_power
 * @property string $chassis
 * @property string $min_price
 * @property string $max_price
 * @property int $own_trailer
 * @property string $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Cargo[] $customers
 * @property-read int|null $customers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TrailerModel[] $trailerModels
 * @property-read int|null $trailer_models_count
 * @method static \Illuminate\Database\Eloquent\Builder|Cargo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cargo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cargo query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cargo whereAdr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cargo whereChassis($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cargo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cargo whereEnginePower($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cargo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cargo whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cargo whereMaxPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cargo whereMinPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cargo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cargo whereOwnTrailer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cargo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cargo whereWeight($value)
 * @mixin \Eloquent
 */
class Cargo extends Model
{
    use HasFactory, HasUuid, HasTranslations, SoftDeletes;

    /**
     * @var string[]
     */
    public $translatable = ['name'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function trailerModels()
    {
        return $this->belongsToMany(TrailerModel::class, 'cargo_trailer_model');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function customers()
    {
        return $this->belongsToMany(Cargo::class, 'customer_cargo');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function markets()
    {
        return $this->hasMany(Market::class);
    }
}
