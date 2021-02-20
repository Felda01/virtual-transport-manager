<?php

namespace App\Jobs;

use App\Models\Driver;
use App\Utilities\GameTimeUtility;
use App\Utilities\QueueJobUtility;
use App\Utilities\StatusUtility;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ManageDriverStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $goSleep;
    public $single;

    /**
     * Create a new job instance.
     *
     * @param bool $goSleep
     * @param bool $single
     */
    public function __construct($goSleep = true, $single = false)
    {
        $this->goSleep = $goSleep;
        $this->single = $single;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $updated = DB::transaction(function () {
            return Driver::where('company_id', '!=', null)
                ->whereIn('status', [StatusUtility::TRAVEL, StatusUtility::IDLE, StatusUtility::READY, StatusUtility::ON_TRAINING])
                ->update(['sleep' => $this->goSleep ? 1 : 0]);
        });

        if ($this->goSleep) {
            $delayMinutes = 14 * 60;
        } else {
            $delayMinutes = 10 * 60;
        }

        if ($updated && !$this->single) {
            QueueJobUtility::dispatch(new ManageDriverStatus(!$this->goSleep), Carbon::parse(GameTimeUtility::gameTimeToRealTime($delayMinutes), 'Europe/Bratislava'));
        }
    }
}
