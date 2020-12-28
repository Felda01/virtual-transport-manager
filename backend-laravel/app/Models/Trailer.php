<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Trailer
 *
 * @property string $id
 * @property string $trailer_model_id
 * @property string $company_id
 * @property string $garage_id
 * @property int $km
 * @property \Illuminate\Support\Carbon $next_service
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Company $company
 * @property-read \App\Models\Garage $garage
 * @property-read \App\Models\TrailerModel $trailerModel
 * @property-read \App\Models\Truck $truck
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer newQuery()
 * @method static \Illuminate\Database\Query\Builder|Trailer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer whereGarageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer whereKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer whereNextService($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer whereTrailerModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trailer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Trailer withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Trailer withoutTrashed()
 * @mixin \Eloquent
 */
class Trailer extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'next_service' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function trailerModel()
    {
        return $this->belongsTo(TrailerModel::class)->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function garage()
    {
        return $this->belongsTo(Garage::class);
    }
}
