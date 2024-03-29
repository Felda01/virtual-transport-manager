<?php

namespace App\Rules;

use App\Models\Truck;
use Illuminate\Contracts\Validation\Rule;

class TruckFreeTrailerSpotRule implements Rule
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
        /** @var Truck $truck */
        $truck = Truck::find($value);

        return !$truck->trailer()->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.truck_free_trailer_spot');
    }
}
