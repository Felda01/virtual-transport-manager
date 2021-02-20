<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\Driver;
use App\Models\Garage;
use App\Models\Trailer;
use App\Models\Truck;
use App\Models\User;
use App\Utilities\GameTimeUtility;
use App\Utilities\QueueJobUtility;
use App\Utilities\TransactionUtility;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class PayFees implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $single;

    /**
     * Create a new job instance.
     *
     * @param bool $single
     */
    public function __construct($single = false)
    {
        $this->single = $single;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $companies = Company::all();

        $companies->each(function ($item, $key) {

            $result = DB::transaction(function () use ($item) {
                $sum = 0;

                $userSalary = User::where('company_id', $item->id)->sum('salary');
                if ($userSalary && $userSalary > 0) {
                    TransactionUtility::create($item, null, $userSalary, 'user_salaries');
                    $sum += $userSalary;
                }

                $driverSalary = Driver::where('company_id', $item->id)->sum('salary');
                if ($driverSalary && $driverSalary > 0) {
                    TransactionUtility::create($item, null, $driverSalary, 'driver_salaries');
                    $sum += $driverSalary;
                }

                $truckData = Truck::where('company_id', $item->id)
                    ->join('truck_models', 'trucks.truck_model_id', '=', 'truck_models.id')
                    ->selectRaw('sum(truck_models.tax) as truck_tax, sum(truck_models.insurance) as truck_insurance')
                    ->first()->toArray();
                if ($truckData && $truckData['truck_tax'] && $truckData['truck_insurance']) {
                    TransactionUtility::create($item, null, $truckData['truck_tax'], 'truck_taxes');
                    TransactionUtility::create($item, null, $truckData['truck_insurance'], 'truck_insurances');
                    $sum += $truckData['truck_tax'] + $truckData['truck_insurance'];
                }

                $trailerData = Trailer::where('company_id', $item->id)
                    ->join('trailer_models', 'trailers.trailer_model_id', '=', 'trailer_models.id')
                    ->selectRaw('sum(trailer_models.tax) as trailer_tax, sum(trailer_models.insurance) as trailer_insurance')
                    ->first()->toArray();
                if ($trailerData && $trailerData['trailer_tax'] && $trailerData['trailer_insurance']) {
                    TransactionUtility::create($item, null, $trailerData['trailer_tax'], 'trailer_taxes');
                    TransactionUtility::create($item, null, $trailerData['trailer_insurance'], 'trailer_insurances');
                    $sum += $trailerData['trailer_tax'] + $trailerData['trailer_insurance'];
                }

                $garageData = Garage::where('company_id', $item->id)
                    ->join('garage_models', 'garages.garage_model_id', '=', 'garage_models.id')
                    ->selectRaw('sum(garage_models.tax) as garage_tax, sum(garage_models.insurance) as garage_insurance')
                    ->first()->toArray();
                if ($garageData && $garageData['garage_tax'] && $garageData['garage_insurance']) {
                    TransactionUtility::create($item, null, $garageData['garage_tax'], 'garage_taxes');
                    TransactionUtility::create($item, null, $garageData['garage_insurance'], 'garage_insurances');
                    $sum += $garageData['garage_tax'] + $garageData['garage_insurance'];
                }

                $oldMoney = $item->money;

                $item->decrement('money', $sum);
                $item->save();

                if ($item->money !== ($oldMoney - $sum)) {
                    throw new \GraphQL\Error\Error(trans('validation.general_exception'));
                }
            });
        });

        $now = Carbon::parse(GameTimeUtility::gameTime(Carbon::now('Europe/Bratislava')));
        $nextMonth = $now->copy()->addMonths();
        $diffMinutes = $nextMonth->diffInRealMinutes($now);

        if (!$this->single) {
            QueueJobUtility::dispatch(new PayFees(), Carbon::parse(GameTimeUtility::gameTimeToRealTime($diffMinutes), 'Europe/Bratislava'));
        }
    }
}
