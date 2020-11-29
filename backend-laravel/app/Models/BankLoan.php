<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BankLoan
 *
 * @property string $id
 * @property string $company_id
 * @property string $bank_loan_type_id
 * @property int $done
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BankLoanType $bankLoanType
 * @method static \Illuminate\Database\Eloquent\Builder|BankLoan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankLoan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankLoan query()
 * @method static \Illuminate\Database\Eloquent\Builder|BankLoan whereBankLoanTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankLoan whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankLoan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankLoan whereDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankLoan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankLoan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BankLoan extends Model
{
    use HasFactory, HasUuid;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bankLoanType()
    {
        return $this->belongsTo(BankLoanType::class);
    }
}
