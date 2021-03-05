<?php


namespace App\Utilities;


use App\Models\Driver;
use App\Models\Garage;
use App\Models\Trailer;
use App\Models\Truck;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class CompanyUtility
 * @package App\Utilities
 */
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

    /**
     * @param $company
     * @return array
     */
    public static function nextPayment($company) {
        $userSalary = User::where('company_id', $company->id)->sum('salary');

        $driverSalary = Driver::where('company_id', $company->id)->sum('salary');

        $truckData = Truck::where('company_id', $company->id)
            ->join('truck_models', 'trucks.truck_model_id', '=', 'truck_models.id')
            ->selectRaw('sum(truck_models.tax) as truck_tax, sum(truck_models.insurance) as truck_insurance')
            ->first()->toArray();

        $trailerData = Trailer::where('company_id', $company->id)
            ->join('trailer_models', 'trailers.trailer_model_id', '=', 'trailer_models.id')
            ->selectRaw('sum(trailer_models.tax) as trailer_tax, sum(trailer_models.insurance) as trailer_insurance')
            ->first()->toArray();

        $garageData = Garage::where('company_id', $company->id)
            ->join('garage_models', 'garages.garage_model_id', '=', 'garage_models.id')
            ->selectRaw('sum(garage_models.tax) as garage_tax, sum(garage_models.insurance) as garage_insurance')
            ->first()->toArray();

        $bankLoans = DB::table('bank_loans')->where('company_id', $company->id)
            ->leftJoin('bank_loan_types', 'bank_loans.bank_loan_type_id', '=', 'bank_loan_types.id')
            ->where('done', false)
            ->sum('bank_loan_types.payment');

        return [
            'userSalary' => $userSalary,
            'driverSalary' => $driverSalary,
            'truck_tax' => $truckData['truck_tax'] ?? 0,
            'truck_insurance' => $truckData['truck_insurance'] ?? 0,
            'trailer_tax' => $trailerData['trailer_tax'] ?? 0,
            'trailer_insurance' => $trailerData['trailer_insurance'] ?? 0,
            'garage_tax' => $garageData['garage_tax'] ?? 0,
            'garage_insurance' => $garageData['garage_insurance'] ?? 0,
            'bank_loan_payment' => $bankLoans
        ];
    }
}
