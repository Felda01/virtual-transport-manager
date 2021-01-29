<?php

namespace App\Rules;

use App\Models\Truck;
use Illuminate\Contracts\Validation\Rule;

class TruckHasTrailerRule implements Rule
{
    public $trailerId;

    /**
     * Create a new rule instance.
     *
     * @param $trailerId
     */
    public function __construct($trailerId)
    {
        $this->trailerId = $trailerId;
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

        return $truck->trailer_id === $this->trailerId;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.truck_has_trailer');
    }
}
