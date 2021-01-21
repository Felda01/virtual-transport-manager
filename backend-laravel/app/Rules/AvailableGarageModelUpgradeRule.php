<?php

namespace App\Rules;

use App\Models\Garage;
use App\Models\GarageModel;
use Illuminate\Contracts\Validation\Rule;

class AvailableGarageModelUpgradeRule implements Rule
{
    public $garageId;

    /**
     * Create a new rule instance.
     *
     * @param $garageId
     */
    public function __construct($garageId)
    {
        $this->garageId = $garageId;
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
        /** @var Garage $currentGarage */
        $currentGarage = Garage::find($this->garageId);
        $currentGarageModel = $currentGarage->garageModel;

        /** @var GarageModel $garageModel */
        $garageModel = GarageModel::find($value);

        return $currentGarageModel->id !== $garageModel->id && $currentGarageModel->truck_count <= $garageModel->truck_count && $currentGarageModel->trailer_count <= $garageModel->trailer_count;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.available_garage_model_upgrade');
    }
}
