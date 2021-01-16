<?php

namespace App\Rules;

use App\Models\Driver;
use Illuminate\Contracts\Validation\ImplicitRule;

class AvailableDriverRecruitmentAgencyRule implements ImplicitRule
{
    public $driverId;

    /**
     * Create a new rule instance.
     *
     * @param $driverId
     */
    public function __construct($driverId)
    {
        $this->driverId = $driverId;
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
        return Driver::where('id', $this->driverId)->where('company_id', null)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.available_driver_recruitment_agency');
    }
}
