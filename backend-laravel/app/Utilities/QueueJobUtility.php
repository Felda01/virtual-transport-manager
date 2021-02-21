<?php


namespace App\Utilities;

use Carbon\Carbon;
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
        Log::info('Job delay: ' . $delay);
        dispatch($job)->delay(Carbon::now()->addMinutes(1));
    }
}
