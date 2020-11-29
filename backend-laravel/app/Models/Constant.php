<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Constant
 *
 * @property string $id
 * @property string $group
 * @property string $name
 * @property string $type
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Constant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Constant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Constant query()
 * @method static \Illuminate\Database\Eloquent\Builder|Constant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Constant whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Constant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Constant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Constant whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Constant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Constant whereValue($value)
 * @mixin \Eloquent
 */
class Constant extends Model
{
    use HasFactory, HasUuid;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
