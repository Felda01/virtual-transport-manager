<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Utilities\FilterUtility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Transaction
 *
 * @property string $id
 * @property string $company_id
 * @property string $user_id
 * @property string $value
 * @property string|null $productable_type
 * @property string|null $productable_id
 * @property string $activity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Company $company
 * @property-read Model|\Eloquent $productable
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereProductableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereProductableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereValue($value)
 * @mixin \Eloquent
 */
class Transaction extends Model
{
    use HasFactory, HasUuid;

    const SYSTEM_USER = 'system';

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
        'user',
        'value',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function productable()
    {
        return $this->morphTo()->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchUser($query, $value)
    {
        if ($value) {
            if ($value === self::SYSTEM_USER) {
                return $query->whereNull('user_id');
            }
            return $query->whereHas('user', function (Builder $query) use ($value) {
                $query->where('id', $value);
            });
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return Builder
     */
    public function searchValue($query, $value)
    {
        $values = FilterUtility::getRangeValues($value);

        if (array_key_exists('min', $values)) {
            $query = $query->where('value', '>=', $values['min']);
        }
        if (array_key_exists('max', $values)) {
            $query = $query->where('value', '<=', $values['max']);
        }

        return $query;
    }
}
