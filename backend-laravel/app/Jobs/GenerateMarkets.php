<?php

namespace App\Jobs;

use App\Models\Market;
use App\Utilities\GameTimeUtility;
use App\Utilities\MarketUtility;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateMarkets implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $gameTime = Carbon::parse(GameTimeUtility::gameTime(Carbon::now('Europe/Bratislava')));

        if ($gameTime->hour > 7 && $gameTime->hour < 17) {
            $deleted = Market::onlyTrashed()->count();

            MarketUtility::reUseSimpleMarkets(max($deleted, 2));
            MarketUtility::newSimpleMarkets(1);
        }
    }
}
