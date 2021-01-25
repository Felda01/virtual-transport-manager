<?php

namespace App\Rules;

use App\Utilities\StatusUtility;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class AvailableModelRule implements Rule
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
        /** @var Model $model */
        $model = $this->model::find($value);

        return StatusUtility::AVAILABLE === $model->status;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.available_model');
    }
}
