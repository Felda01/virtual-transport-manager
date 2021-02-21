<?php

namespace App\Rules;

use App\Models\Order;
use App\Models\Truck;
use Illuminate\Contracts\Validation\Rule;

class TruckForOrderRule implements Rule
{
    public $orderId;

    /**
     * Create a new rule instance.
     *
     * @param $orderId
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
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
        /** @var Order $order */
        $order = Order::with('market.cargo.trailerModels')
            ->where('id', $this->orderId)
            ->first();

        $query = Truck::trucksForOrder(Truck::query(), $order->market->cargo);
        return $query->where('id', $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.truck_for_order');
    }
}
