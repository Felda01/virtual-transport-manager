<?php

namespace App\Jobs;

use App\Events\RefreshMarketQuery;
use App\Models\Market;
use App\Utilities\BroadcastUtility;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteMarket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $market;

    /**
     * Create a new job instance.
     *
     * @param Market $market
     */
    public function __construct(Market $market)
    {
        $this->market = $market;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        if ($this->market->orders()->count() > 0) {
            BroadcastUtility::broadcast(new RefreshMarketQuery());
            return;
        }

        BroadcastUtility::broadcast(new RefreshMarketQuery());
        $this->market->delete();
    }
}
