<?php

namespace App\Jobs;

use App\Events\ProcessTransaction;
use App\Models\Company;
use App\Models\Order;
use App\Models\RoadTrip;
use App\Utilities\BroadcastUtility;
use App\Utilities\GameTimeUtility;
use App\Utilities\PathUtility;
use App\Utilities\StatusUtility;
use App\Utilities\TransactionUtility;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class FinishOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;

    /**
     * Create a new job instance.
     *
     * @param $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $company = Company::find($this->order->company_id);

        DB::transaction(function () use ($company) {
            $sum = 0;

            /** @var RoadTrip $roadTrip */
            $roadTrip = RoadTrip::find($this->order->road_trip_id);
            $roadTrip->damage = PathUtility::calculateDamage();

            $carbonNow = Carbon::now('Europe/Bratislava');

            if ($carbonNow > $this->order->market->expires_at) {
                $roadTrip->status = StatusUtility::EXPIRED;
                $orderPrice = 0;
            } else {
                $roadTrip->status = StatusUtility::FINISHED;
                $orderPrice = $this->order->market->price * ((100 - $roadTrip->damage) / 100);
            }

            $roadTripSaved = $roadTrip->save();

            TransactionUtility::create($company, $this->order, $orderPrice, 'order_finish');
            TransactionUtility::create($company, $this->order, -1 * $roadTrip->fees, 'order_road_fees');

            $sum += $orderPrice - $roadTrip->fees;

            $oldMoney = $company->money;
            $company->increment('money', $sum);
            $company->save();

            $now = Carbon::parse(GameTimeUtility::gameTime($carbonNow), 'Europe/Bratislava');

            $driversUpdated = $this->order->drivers()->update([
                'location_id' => $this->order->market->location_to,
                'status' => StatusUtility::READY,
                'sleep' => $now->hour < 7 || $now->hour > 16
            ]);

            if (!$roadTripSaved || !$driversUpdated || $company->money !== ($oldMoney + $sum)) {
                throw new \GraphQL\Error\Error(trans('validation.general_exception'));
            }
        });

        BroadcastUtility::broadcast(new ProcessTransaction($company));
    }
}
