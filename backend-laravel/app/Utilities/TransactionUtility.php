<?php


namespace App\Utilities;


use App\Models\Company;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TransactionUtility
 * @package App\Utilities
 */
class TransactionUtility
{
    /**
     * @param Company $company
     * @param Model $product
     * @param $price
     * @param $activity
     * @return Transaction
     */
    public static function create($company, $product, $price, $activity)
    {
        /** @var User $user */
        $user = User::current();

        $transaction = new Transaction();
        $transaction->company_id = $company->id;
        $transaction->user_id = $user->id;
        $transaction->value = $price;
        $transaction->activity = $activity;
        $transaction->productable()->associate($product);

        $transaction->save();

        return $transaction;
    }
}
