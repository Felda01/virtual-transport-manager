<?php

namespace App\Jobs;

use App\Events\ProcessTransaction;
use App\Events\RefreshQuery;
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
use Illuminate\Support\Facades\Log;

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

            $emissionClassMultipliers = config('constants.truck_emission_classes_fee_multiplier');

            $roadFees = $roadTrip->fees * $emissionClassMultipliers[$this->order->truck->truckModel->emission_class - 3];

            TransactionUtility::create($company, $this->order, $orderPrice, 'order_finish');
            TransactionUtility::create($company, $this->order, -1 * $roadFees, 'order_road_fees');

            $sum += $orderPrice - $roadFees;

            $oldMoney = $company->money;
            $company->increment('money', $sum);
            $company->save();

            $now = Carbon::parse(GameTimeUtility::gameTime($carbonNow), 'Europe/Bratislava');

            $driversUpdated = $this->order->drivers()->update([
                'location_id' => $this->order->market->location_to,
                'status' => StatusUtility::READY,
                'sleep' => config('app.testing') ? false : ($now->hour < 7 || $now->hour > 16)
            ]);

            $truck = $this->order->truck;
            $truck->increment('km', $roadTrip->km);
            $truckUpdated = $truck->save();

            $trailer = $truck->trailer;
            $trailer->increment('km', $roadTrip->km);
            $trailerSaved = $trailer->save();

            if (!$roadTripSaved || !$driversUpdated || !$truckUpdated || !$trailerSaved || $company->money !== ($oldMoney + $sum)) {
                throw new \GraphQL\Error\Error(trans('validation.general_exception'));
            }
        }, 5);

        BroadcastUtility::broadcast(new ProcessTransaction($company));
        BroadcastUtility::broadcast(new RefreshQuery($company, 'Order', $this->order->id));
    }
}
