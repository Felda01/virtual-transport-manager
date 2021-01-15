<?php


namespace App\Utilities;

/**
 * Class StatusUtility
 * @package App\Utilities
 */
class StatusUtility
{
    const DELIVERY_FROM_SHOP = 0;
    const AVAILABLE = 1;
    const ON_ROAD = 2;
    const ASSIGNED = 3;


    /**
     * @return int[]
     */
    public static function truck()
    {
        return [
            self::DELIVERY_FROM_SHOP,
            self::AVAILABLE,
            self::ASSIGNED,
            self::ON_ROAD,
        ];
    }

    /**
     * @return int[]
     */
    public static function trailer()
    {
        return [
            self::DELIVERY_FROM_SHOP,
            self::AVAILABLE,
            self::ASSIGNED,
            self::ON_ROAD,
        ];
    }

    /**
     * @return int[]
     */
    public static function all()
    {
        return [
            self::DELIVERY_FROM_SHOP,
            self::AVAILABLE,
            self::ASSIGNED,
            self::ON_ROAD,
        ];
    }
}
