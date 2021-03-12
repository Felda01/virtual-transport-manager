<?php

namespace App\Rules;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

class ExistsCompanyOwnerRule implements Rule
{
    public $userId;

    /**
     * Create a new rule instance.
     *
     * @param $userId
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
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
        $owner = Role::where('name', Role::OWNER)->first();

        if (!in_array($owner->id, $ids)) {
            $company = Company::currentCompany();
            return User::where('company_id', $company->id)->whereHas('roles', function (Builder $query) use ($owner) {
                $query->where('name', Role::OWNER);
            })->where('id', '!=', $this->userId)->count() > 0;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.exists_company_owner');
    }
}
