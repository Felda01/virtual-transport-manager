<?php

namespace App\Rules;

use App\Models\Company;
use App\Models\Garage;
use Illuminate\Contracts\Validation\Rule;

class AvailableLocationRule implements Rule
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
        $company = Company::currentCompany();

        return ! Garage::whereCompanyId($company->id)->where('location_id', $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.available_location');
    }
}
