<?php

namespace App\Rules;

use App\Models\Truck;
use Illuminate\Contracts\Validation\Rule;

class TruckHasDriverRule implements Rule
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
        /** @var Truck $truck */
        $truck = Truck::find($value);

        return $truck->drivers()->where('id', $this->driverId)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.truck_has_driver');
    }
}
