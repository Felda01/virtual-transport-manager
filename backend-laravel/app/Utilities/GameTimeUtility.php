<?php


namespace App\Utilities;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

/**
 * Class GameTimeUtility
 * @package App\Utilities
 */
class GameTimeUtility
{
    /**
     * Convert real time to game time
     *
     * @param $time
     * @return string
     */
    public static function gameTime($time)
    {
        $start = Carbon::parse(Config::get('constants.start_game_time'), 'Europe/Bratislava');

        $currentTime = Carbon::parse($time);

        $diffSeconds = $start->diffInRealSeconds($currentTime);

        $gameDiffSeconds = Config::get('constants.time_acceleration') * $diffSeconds;

        return $start->addSeconds($gameDiffSeconds)->toDateTimeString();
    }

    /**
     * Return real seconds from game minutes
     *
     * @param $minutes
     * @return int
     */
    public static function realTimeToGameTime($minutes)
    {
        return (int) (ceil($minutes * 60 / Config::get('constants.time_acceleration')));
    }

    /**
     * Return game seconds from real minutes
     *
     * @param $minutes
     * @return int
     */
    public static function gameTimeToRealTime($minutes)
    {
        return (int) (ceil($minutes / 60 * Config::get('constants.time_acceleration')));
    }

    /**
     * Return real time with game hours
     *
     * @param $minutes
     * @param string|null $time
     * @return string
     */
    public static function addTimeToRealTime($minutes, string $time = null)
    {
        if ($time == null) {
            $time = Carbon::now(Config::get('app.timezone'));
        }
        return Carbon::parse($time)->addSeconds(static::realTimeToGameTime($minutes))->toDateTimeString();
    }
}
