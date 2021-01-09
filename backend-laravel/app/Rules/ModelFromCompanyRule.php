<?php

namespace App\Rules;

use App\Models\Company;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class ModelFromCompanyRule implements Rule
{
    public $model;

    /**
     * Create a new rule instance.
     *
     * @param $model
     */
    public function __construct($model)
    {
        /** @var Model model */
        $this->model = "App\\Models\\{$model}";
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
        $model = $this->model::find($value);

        $company = Company::currentCompany();

        return $company->id === $model->company_id;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.model_from_company');
    }
}
