<?php

namespace App\Rules;

use App\Models\Driver;
use Illuminate\Contracts\Validation\Rule;

class DriverHasTruckRule implements Rule
{
    public $truckId;

    /**
     * Create a new rule instance.
     *
     * @param $truckId
     */
    public function __construct($truckId)
    {
        $this->truckId = $truckId;
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

        return $driver->truck_id === $this->truckId;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.driverAssignedTruck');
    }
}
