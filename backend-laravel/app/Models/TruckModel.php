<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    use HasFactory, HasUuid;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
