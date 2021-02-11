<?php

namespace App\Rules;

use App\Models\Market;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class AvailableMarketRule implements Rule
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
        /** @var Market $market */
        $market = Market::find($value);

        return $market->amount > 0 && Carbon::parse($market->expires_at)->gt(Carbon::now('Europe/Bratislava'));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.available_market');
    }
}
