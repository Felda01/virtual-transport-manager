<?php

namespace App\Rules;

use App\Models\Route;
use Illuminate\Contracts\Validation\Rule;

class UniqueLocationsRule implements Rule
{
    public $location;

    /**
     * Create a new rule instance.
     *
     * @param $location
     */
    public function __construct($location)
    {
        $this->location = $location;
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
        return !Route::where(function($query) use ($value) {
            $query->where('location1_id', $value)
                ->where('location2_id', $this->location);
        })->orWhere(function($query) use ($value) {
            $query->where('location1_id', $this->location)
                ->where('location2_id', $value);
        })->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The route has already been taken.';
    }
}
