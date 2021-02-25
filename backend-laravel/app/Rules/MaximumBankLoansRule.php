<?php

namespace App\Rules;

use App\Models\BankLoan;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\ImplicitRule;

class MaximumBankLoansRule implements ImplicitRule
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
        /** @var User $user */
        $user = User::current();

        return config('constants.max_bank_loans') > BankLoan::where('company_id', $user->company_id)->where('done', false)->count();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.maximum_bank_loans');
    }
}
