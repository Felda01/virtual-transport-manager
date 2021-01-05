<?php


namespace App\Utilities;


use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CompanyUtility
{
    /**
     * @param Model $model
     * @param $id
     * @return bool
     */
    public static function can(Model $model, $id)
    {
        /** @var User $user */
        $user = User::find($id);

        return $model->company_id === $user->company_id;
    }
}
