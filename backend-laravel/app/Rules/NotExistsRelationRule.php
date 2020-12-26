<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NotExistsRelationRule implements ImplicitRule
{
    public $model;
    public $relation;
    public $type;

    /**
     * Create a new rule instance.
     *
     * @param $model
     * @param $relation
     * @param string $type
     */
    public function __construct($model, $relation, $type = 'delete')
    {
        /** @var Model model */
        $this->model = "App\\Models\\{$model}";
        $this->relation = $relation;
        $this->type = $type;
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
        return 'The validation error message. '. $this->type;
    }
}
