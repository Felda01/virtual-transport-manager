<?php

namespace App\Rules;

use App\Models\Trailer;
use Illuminate\Contracts\Validation\Rule;

class CanDeleteTrailerRule implements Rule
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
        /** @var Trailer $trailer */
        $trailer = Trailer::find($value);

        return !$trailer->truck()->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.can_delete_trailer');
    }
}