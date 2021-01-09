<?php

namespace App\Rules;

use App\Models\Garage;
use Illuminate\Contracts\Validation\Rule;

class AvailableGarageSpotRule implements Rule
{
    public $spotType;

    /**
     * Create a new rule instance.
     *
     * @param $spotType
     */
    public function __construct($spotType)
    {
        $this->spotType = $spotType;
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

        if ($this->spotType === 'truck') {
            return $garage->trucks()->count() < $garage->garageModel->truck_count;
        }

        if ($this->spotType === 'trailer') {
            return $garage->trailers()->count() < $garage->garageModel->trailer_count;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.available_garage_spot');
    }
}
