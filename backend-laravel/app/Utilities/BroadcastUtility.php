<?php


namespace App\Utilities;

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
        broadcast($event)->toOthers();
    }
}
