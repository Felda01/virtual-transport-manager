<?php

namespace App\Jobs;

use App\Events\RefreshMarketQuery;
use App\Events\RefreshQuery;
use App\Models\Market;
use App\Utilities\BroadcastUtility;
use App\Utilities\GameTimeUtility;
use App\Utilities\MarketUtility;
use App\Utilities\QueueJobUtility;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateMarkets implements ShouldQueue
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
     * @throws \Exception
     */
    public function handle()
    {
        $deleted = Market::onlyTrashed()->count();

        MarketUtility::reUseSimpleMarkets(min($deleted, 2));
        MarketUtility::newSimpleMarkets(3);

        $gameTime = Carbon::parse(GameTimeUtility::gameTime(Carbon::now('Europe/Bratislava')));
        BroadcastUtility::broadcast(new RefreshMarketQuery());

        if (!$this->single) {
            if ($gameTime->hour > 7 && $gameTime->hour < 16) {
                QueueJobUtility::dispatch(new GenerateMarkets(), Carbon::parse(GameTimeUtility::gameTimeToRealTime(60), 'Europe/Bratislava'));
            } else {
                QueueJobUtility::dispatch(new GenerateMarkets(), Carbon::parse(GameTimeUtility::gameTimeToRealTime(15 * 60), 'Europe/Bratislava'));
            }
        }
    }
}
