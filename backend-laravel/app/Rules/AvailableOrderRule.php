<?php

namespace App\Rules;

use App\Models\Order;
use App\Utilities\StatusUtility;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class AvailableOrderRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $order = Order::with(['roadTrip', 'market'])->where('id', $value)->first();

        return $order->roadTrip->status === StatusUtility::WAITING_FOR_DRIVERS && $order->market->expires_at > Carbon::now('Europe/Bratislava');
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.available_order');
    }
}
