<?php


namespace App\Utilities;

use Carbon\Carbon;

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
        dispatch($job)->delay(Carbon::now()->addMinutes(1));
    }
}
