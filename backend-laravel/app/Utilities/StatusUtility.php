<?php


namespace App\Utilities;

/**
 * Class StatusUtility
 * @package App\Utilities
 */
class StatusUtility
{
    const DELIVERY_FROM_SHOP = 0;
    const IDLE = 1;
    const ON_ROAD = 2;
    const ON_DUTY = 3;
    const FINISHED = 4;
    const READY = 5;
    const SLEEP = 6;
    const TRAVEL = 7;
    const ON_TRAINING = 8;
    const WAITING_FOR_DRIVERS = 9;


    /**
     * @return int[]
     */
    public static function truck()
    {
        return [
            self::DELIVERY_FROM_SHOP,
            self::IDLE,
            self::ON_DUTY,
        ];
    }

    /**
     * @return int[]
     */
    public static function trailer()
    {
        return [
            self::DELIVERY_FROM_SHOP,
            self::IDLE,
            self::ON_DUTY,
        ];
    }

    public static function driver()
    {
        return [
            self::TRAVEL,
            self::IDLE,
            self::READY,
            self::ON_ROAD,
            self::SLEEP,
            self::ON_TRAINING,
        ];
    }

    /**
     * @return int[]
     */
    public static function order()
    {
        return [
            self::WAITING_FOR_DRIVERS,
            self::ON_ROAD
        ];
    }

    /**
     * @return int[]
     */
    public static function all()
    {
        return [
            self::DELIVERY_FROM_SHOP,
            self::IDLE,
            self::ON_DUTY,
            self::ON_ROAD,
            self::FINISHED,
        ];
    }
}
