<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class ModelStatusRule implements Rule
{
    public $model;

    public $statuses;

    /**
     * Create a new rule instance.
     *
     * @param $model
     * @param $statuses
     */
    public function __construct($model, $statuses)
    {
        /** @var Model model */
        $this->model = "App\\Models\\{$model}";

        $this->statuses = $statuses;
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

        return in_array($model->status, $this->statuses);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.model_status');
    }
}
