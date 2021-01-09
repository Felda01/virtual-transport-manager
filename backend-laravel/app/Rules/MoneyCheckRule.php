<?php

namespace App\Rules;

use App\Models\Company;
use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Database\Eloquent\Model;

class MoneyCheckRule implements ImplicitRule
{
    public $model;
    public $id;
    public $column;

    /**
     * Create a new rule instance.
     *
     * @param $model
     * @param string $column
     */
    public function __construct($model, $id, $column = 'price')
    {
        /** @var Model model */
        $this->model = "App\\Models\\{$model}";
        $this->id = $id;
        $this->column = $column;
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
        $company = Company::currentCompany();

        $model = $this->model::find($this->id);

        if ($model) {
            return $model->{$this->column} <= $company->money;
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
        return trans('validation.not_enough_money');
    }
}
