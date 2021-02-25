<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\BankLoan
 *
 * @property string $id
 * @property string $company_id
 * @property string $bank_loan_type_id
 * @property int $done
 * @property int $paid
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
    use HasFactory, HasUuid, LogsActivity, SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    protected static $logUnguarded = true;

    /**
     * @var bool
     */
    protected static $logOnlyDirty = true;

    /**
     * @param Activity $activity
     * @param string $eventName
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->description = "activity.{$eventName}.bankLoan";
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bankLoanType()
    {
        return $this->belongsTo(BankLoanType::class)->withTrashed();
    }
}
