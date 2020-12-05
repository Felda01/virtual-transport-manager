<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class NotExistsRelationRule implements Rule
{
    public $model;
    public $relation;

    /**
     * Create a new rule instance.
     *
     * @param $model
     * @param $relation
     */
    public function __construct($model, $relation)
    {
        /** @var Model model */
        $this->model = "App\\Models\\{$model}";
        $this->relation = $relation;
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
        return $this->model::doesnthave($this->relation)->where('id', $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
