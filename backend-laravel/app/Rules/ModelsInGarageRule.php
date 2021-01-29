<?php

namespace App\Rules;

use App\Models\Driver;
use App\Models\Trailer;
use App\Models\Truck;
use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Database\Eloquent\Model;

class ModelsInGarageRule implements ImplicitRule
{
    public $model;

    public $modelId;

    public $compareModel;

    public $compareModelId;

    public $modelString;

    public $compareModelString;

    /**
     * Create a new rule instance.
     *
     * @param $model
     * @param $modelId
     * @param $compareModel
     * @param $compareModelId
     */
    public function __construct($model, $modelId, $compareModel, $compareModelId)
    {
        /** @var Model model */
        $this->model = "App\\Models\\{$model}";

        $this->modelId = $modelId;

        $this->compareModel = "App\\Models\\{$compareModel}";

        $this->compareModelId = $compareModelId;

        $this->modelString = $model;

        $this->compareModelString = $compareModel;
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
        $model = $this->model::find($this->modelId);

        $compareModel = $this->compareModel::find($this->compareModelId);

        switch (true) {
            case ($model instanceof Driver && $compareModel instanceof Truck): {
                $garageLocationId = $compareModel->garage->location_id;
                return $model->location_id === $garageLocationId && $model->garage->location_id === $garageLocationId;
            }
            case ($model instanceof Trailer && $compareModel instanceof Truck): {
                return $model->garage->location_id === $compareModel->garage->location_id;
            }
            default: {
                return false;
            }
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.model_in_garage', ['model' => $this->modelString, 'compareModel' => $this->compareModelString]);
    }
}
