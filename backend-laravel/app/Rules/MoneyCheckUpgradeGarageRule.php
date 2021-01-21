<?php

namespace App\Rules;

use App\Models\Company;
use App\Models\Garage;
use App\Models\GarageModel;
use Illuminate\Contracts\Validation\Rule;

class MoneyCheckUpgradeGarageRule implements Rule
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

        $company = Company::currentCompany();

        if (!$currentGarage || !$garageModel) {
            return false;
        }

        return ($garageModel->price - $currentGarageModel->price) <= $company->money;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.not_enough_money');
    }
}
