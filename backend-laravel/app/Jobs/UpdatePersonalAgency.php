<?php

namespace App\Jobs;

use App\Events\RefreshQuery;
use App\Models\Company;
use App\Models\Driver;
use App\Utilities\BroadcastUtility;
use App\Utilities\GameTimeUtility;
use App\Utilities\QueueJobUtility;
use App\Utilities\RecruitmentAgencyUtility;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdatePersonalAgency implements ShouldQueue
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
        $countDrivers = Driver::where('company_id', null)->count();

        if ($countDrivers < 10) {
            RecruitmentAgencyUtility::newDriver(4);
        }

        $companies = Company::all();

        $companies->each(function ($item, $key) {
            BroadcastUtility::broadcast(new RefreshQuery($item, 'Recruitment'));
        });

        if (!$this->single) {
            QueueJobUtility::dispatch(new UpdatePersonalAgency(), Carbon::parse(GameTimeUtility::addTimeToRealTime(24 * 60), 'Europe/Bratislava'));
        }
    }
}
