<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LanguageLine
 *
 * @property string $id
 * @property string $group
 * @property string $key
 * @property string $text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageLine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageLine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageLine query()
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageLine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageLine whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageLine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageLine whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageLine whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageLine whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LanguageLine extends Model
{
    use HasFactory, HasUuid;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
