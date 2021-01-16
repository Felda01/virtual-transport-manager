<?php

namespace App\Jobs;

use App\Models\Driver;
use App\Utilities\RecruitmentAgencyUtility;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdatePersonalAgency implements ShouldQueue
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
     */
    public function handle()
    {
        $countDrivers = Driver::where('company_id', null)->count();

        if ($countDrivers < 5) {
            RecruitmentAgencyUtility::newDriver(2);
        }
    }
}
