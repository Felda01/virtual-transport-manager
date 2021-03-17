<?php

namespace App\Rules;

use App\Models\Driver;
use Illuminate\Contracts\Validation\Rule;

class DriverNotInGarageRule implements Rule
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
        /** @var Driver $driver */
        $driver = Driver::find($value);

        return $driver->location_id !== $driver->garage->location_id;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.driver_not_in_garage');
    }
}
