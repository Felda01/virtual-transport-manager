<?php

namespace App\Rules;

use App\Models\BankLoan;
use App\Models\Company;
use Illuminate\Contracts\Validation\Rule;

class MoneyCheckBankLoanRepayRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        /** @var Company $company */
        $company = Company::currentCompany();

        /** @var BankLoan $bankLoan */
        $bankLoan = BankLoan::find($value);

        $bankLoanType = $bankLoan->bankLoanType;

        $leftToRepay = $bankLoanType->payment * ($bankLoanType->period - $bankLoan->paid);

        return $leftToRepay <= $company->money;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.not_enough_money');
    }
}
