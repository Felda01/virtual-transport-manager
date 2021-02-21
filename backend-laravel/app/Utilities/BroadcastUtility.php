<?php


namespace App\Utilities;

use App\Events\ProcessTransaction;

/**
 * Class BroadcastUtility
 * @package App\Utilities
 */
class BroadcastUtility
{
    /**
     * @param $event
     */
    public static function broadcast($event)
    {
        if ($event instanceof ProcessTransaction) {
            broadcast($event);
        } else {
            broadcast($event)->toOthers();
        }
    }
}
