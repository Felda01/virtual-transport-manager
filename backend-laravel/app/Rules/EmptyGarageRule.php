<?php

namespace App\Rules;

use App\Models\Garage;
use Illuminate\Contracts\Validation\Rule;

class EmptyGarageRule implements Rule
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
        /** @var Garage $garage */
        $garage = Garage::find($value);

        return $garage->drivers()->count() === 0 && $garage->trailers()->count() === 0 && $garage->trucks()->count() === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.empty_garage');
    }
}
