<?php

namespace App\Jobs;

use App\Events\ProcessTransaction;
use App\Models\Company;
use App\Models\Driver;
use App\Models\Garage;
use App\Models\Trailer;
use App\Models\Truck;
use App\Models\User;
use App\Utilities\BroadcastUtility;
use App\Utilities\CompanyUtility;
use App\Utilities\GameTimeUtility;
use App\Utilities\QueueJobUtility;
use App\Utilities\TransactionUtility;
use Carbon\Carbon;
use Exception;
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

                $data = CompanyUtility::nextPayment($item);

                if ($data['userSalary'] && $data['userSalary'] > 0) {
                    TransactionUtility::create($item, null, -1 * $data['userSalary'], 'user_salaries');
                    $sum += $data['userSalary'];
                }

                if ($data['driverSalary'] && $data['driverSalary'] > 0) {
                    TransactionUtility::create($item, null, -1 * $data['driverSalary'], 'driver_salaries');
                    $sum += $data['driverSalary'];
                }


                if ($data['truck_tax'] && $data['truck_insurance']) {
                    TransactionUtility::create($item, null, -1 * $data['truck_tax'], 'truck_taxes');
                    TransactionUtility::create($item, null, -1 * $data['truck_insurance'], 'truck_insurances');
                    $sum += $data['truck_tax'] + $data['truck_insurance'];
                }

                if ($data['trailer_tax'] && $data['trailer_insurance']) {
                    TransactionUtility::create($item, null, -1 * $data['trailer_tax'], 'trailer_taxes');
                    TransactionUtility::create($item, null, -1 * $data['trailer_insurance'], 'trailer_insurances');
                    $sum += $data['trailer_tax'] + $data['trailer_insurance'];
                }

                if ($data['garage_tax'] && $data['garage_insurance']) {
                    TransactionUtility::create($item, null, -1 * $data['garage_tax'], 'garage_taxes');
                    TransactionUtility::create($item, null, -1 * $data['garage_insurance'], 'garage_insurances');
                    $sum += $data['garage_tax'] + $data['garage_insurance'];
                }

                $oldMoney = $item->money;

                $item->decrement('money', $sum);
                $item->save();

                if ($item->money !== ($oldMoney - $sum)) {
                    throw new Exception(trans('validation.general_exception'));
                }
            });

            BroadcastUtility::broadcast(new ProcessTransaction($item));
        });

        $now = Carbon::parse(GameTimeUtility::gameTime(Carbon::now('Europe/Bratislava')));
        $nextMonth = $now->copy()->addMonths();
        $diffMinutes = $nextMonth->diffInRealMinutes($now);

        if (!$this->single) {
            QueueJobUtility::dispatch(new PayFees(), Carbon::parse(GameTimeUtility::addTimeToRealTime($diffMinutes), 'Europe/Bratislava'));
        }
    }
}
