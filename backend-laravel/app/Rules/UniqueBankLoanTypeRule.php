<?php

namespace App\Rules;

use App\Models\BankLoanType;
use Illuminate\Contracts\Validation\ImplicitRule;

class UniqueBankLoanTypeRule implements ImplicitRule
{
    /**
     * @var array
     */
    public $arguments = [];

    public $except = '';

    /**
     * Create a new rule instance.
     * @param $arguments
     * @param string $except
     */
    public function __construct($arguments, $except = '')
    {
        $this->arguments = $arguments;
        $this->except = $except;
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
        $query = BankLoanType::where('value', $this->arguments['value'])
            ->where('payment', $this->arguments['payment'])
            ->where('period', $this->arguments['period']);

        if ($this->except) {
            $query->where('id', '!=', $this->except);
        }

        return ! $query->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.unique_bank_loan_type');
    }
}
