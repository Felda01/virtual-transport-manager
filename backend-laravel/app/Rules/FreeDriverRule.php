<?php

namespace App\Rules;

use App\Models\Driver;
use App\Utilities\StatusUtility;
use Illuminate\Contracts\Validation\Rule;

class FreeDriverRule implements Rule
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

        return in_array($driver->status, [StatusUtility::AVAILABLE]);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.free_driver');
    }
}
