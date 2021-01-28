<?php

namespace App\Rules;

use App\Models\Trailer;
use Illuminate\Contracts\Validation\Rule;

class TrailerHasTruckRule implements Rule
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
        /** @var Trailer $trailer */
        $trailer = Trailer::find($value);

        return $trailer->truck()->where('id', $this->truckId)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.trailerAssignedTruck');
    }
}
