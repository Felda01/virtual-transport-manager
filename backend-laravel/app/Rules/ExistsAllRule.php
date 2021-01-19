<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ExistsAllRule implements Rule
{
    /** @var Model string  */
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
        $idsInput = explode(',', $value);

        $ids = array_unique($idsInput);

        $query = $this->model::query();

        foreach ($ids as $id) {
            $query = $query->orWhere('id', $id);
        }

        return $query->count() === count($idsInput);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.exists_all');
    }
}
