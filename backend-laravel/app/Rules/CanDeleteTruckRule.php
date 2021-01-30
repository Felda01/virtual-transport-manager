<?php

namespace App\Rules;

use App\Models\Truck;
use Illuminate\Contracts\Validation\Rule;

class CanDeleteTruckRule implements Rule
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

        return $truck->drivers()->count() === 0 && !$truck->trailer()->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.can_delete_truck');
    }
}
