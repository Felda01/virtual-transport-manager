<?php


namespace App\Utilities;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

/**
 * Class QueueJobUtility
 * @package App\Utilities
 */
class QueueJobUtility
{
    /**
     * @param $job
     * @param $delay
     */
    public static function dispatch($job, $delay)
    {
        if (App::environment('local')) {
            Log::info('Job delay: ' . $delay);
            dispatch($job)->delay(Carbon::now()->addMinutes(1));
        } else {
            dispatch($job)->delay($delay);
        }
    }
}
