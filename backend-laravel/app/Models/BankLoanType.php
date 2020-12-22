<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\BankLoanType
 *
 * @property string $id
 * @property int $value
 * @property int $payment
 * @property int $period
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|BankLoanType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankLoanType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankLoanType query()
 * @method static \Illuminate\Database\Eloquent\Builder|BankLoanType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankLoanType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankLoanType wherePayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankLoanType wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankLoanType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankLoanType whereValue($value)
 * @mixin \Eloquent
 */
class BankLoanType extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bankLoans()
    {
        return $this->hasMany(BankLoan::class);
    }
}
