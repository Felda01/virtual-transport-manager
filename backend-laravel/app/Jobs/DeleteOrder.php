<?php

namespace App\Jobs;

use App\Events\ProcessTransaction;
use App\Events\RefreshQuery;
use App\Models\Company;
use App\Models\Order;
use App\Models\RoadTrip;
use App\Utilities\BroadcastUtility;
use App\Utilities\StatusUtility;
use App\Utilities\TransactionUtility;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class DeleteOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;

    /**
     * Create a new job instance.
     *
     * @param $order
     */
    public function __construct($order)
    {
        /** @var Order order */
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->order->roadTrip->status === StatusUtility::WAITING_FOR_DRIVERS) {
            /** @var Company $company */
            $company = Company::find($this->order->company_id);

            DB::transaction(function() use ($company) {
                /** @var RoadTrip $roadTrip */
                $roadTrip = RoadTrip::find($this->order->road_trip_id);
                $roadTrip->status = StatusUtility::EXPIRED;
                $roadTrip->save();

                $oldMoney = $company->money;
                $price = config('constants.expired_order');

                $transaction = TransactionUtility::create($company, $this->order, -1 * $price, 'order_expired');

                $company->decrement('money', $price);
                $company->save();

                if (!$transaction || $company->money !== ($oldMoney - $price)) {
                    throw new Exception(trans('validation.general_exception'));
                }
            });

            BroadcastUtility::broadcast(new ProcessTransaction($company));
            BroadcastUtility::broadcast(new RefreshQuery($company, 'Order', $this->order->id));
        }
        return;
    }
}
