<?php


namespace App\Utilities;

use App\Jobs\DeleteMarket;
use App\Models\Cargo;
use App\Models\Customer;
use App\Models\Location;
use App\Models\Market;
use App\Models\Route;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Class MarketUtility
 * @package App\Utilities
 */
class MarketUtility
{
    /**
     * @param int $count
     * @throws \Exception
     */
    public static function newSimpleMarkets($count = 1)
    {
        activity()->disableLogging();

        $randomLocations = Location::where('is_city', 1)->inRandomOrder()->limit($count * 2)->get(['id']);
        $randomLocations = $randomLocations->toArray();

        $randomCustomers = Customer::inRandomOrder()->limit($count * 2)->get(['id']);
        $randomCustomers = $randomCustomers->toArray();

        $randomCargos = Cargo::inRandomOrder()->limit($count)->get(['id']);
        $randomCargos = $randomCargos->toArray();

        $forCount = min($count, floor(count($randomLocations) / 2));
        for ($i = 0; $i < $forCount; $i++){
            $weeks = random_int(3, 8);

            $expiresAt = GameTimeUtility::addTimeToRealTime($weeks * 7 * 24 * 60);
            $expiresAtCarbon = Carbon::parse($expiresAt);

            $locationFrom = Location::find($randomLocations[$i * 2]['id']);
            $locationTo = Location::find($randomLocations[$i * 2 + 1]['id']);

            $km = Route::getDistanceBetween($locationFrom, $locationTo);

            /** @var Cargo $cargo */
            $cargo = Cargo::find($randomCargos[$i]['id']);

            $market = new Market;
            $market->location_from = $randomLocations[$i * 2]['id'];
            $market->location_to = $randomLocations[$i * 2 + 1]['id'];
            $market->customer_from = $randomCustomers[$i * 2]['id'];
            $market->customer_to = $randomCustomers[$i * 2 + 1]['id'];
            $market->cargo_id = $cargo->id;
            $market->company_id = null;
            $market->price = self::frand($cargo->min_price, $cargo->max_price) * $km;
            $market->km = $km;
            $market->amount = $cargo->weight * random_int(5, 15);
            $market->frequency = 0;
            $market->count_of_repetitions = 0;
            $market->expires_at = $expiresAtCarbon;

            $market->save();

            QueueJobUtility::dispatch(new DeleteMarket($market), $expiresAtCarbon);
        }

        activity()->enableLogging();
    }

    /**
     * @param int $count
     * @throws \Exception
     */
    public static function reUseSimpleMarkets($count = 1)
    {
        activity()->disableLogging();

        $deletedMarkets = Market::onlyTrashed()->inRandomOrder()->limit($count)->get(['id']);
        $deletedMarkets = $deletedMarkets->toArray();
        $randomLocations = Location::where('is_city', true)->whereHas('drivers')->inRandomOrder()->limit($count * 2)->get(['id']);

        if ($randomLocations->count() < $count * 2) {
            $randomLocationsWithoutDrivers = Location::where('is_city', true)->doesntHave('drivers')->inRandomOrder()->limit($count * 2 - $randomLocations->count())->get(['id']);
            $randomLocations = $randomLocations->concat($randomLocationsWithoutDrivers);
        }
        $randomLocations = $randomLocations->toArray();

        $forCount = min(count($deletedMarkets), floor(count($randomLocations) / 2));
        for ($i = 0; $i < $forCount; $i++){
            /** @var Market $deletedMarket */
            $deletedMarket = Market::onlyTrashed()->find($deletedMarkets[$i]['id']);

            $weeks = random_int(3, 8);

            $expiresAt = GameTimeUtility::addTimeToRealTime($weeks * 7 * 24 * 60);
            $expiresAtCarbon = Carbon::parse($expiresAt);

            $cargo = $deletedMarket->cargo;

            $locationFrom = Location::find($randomLocations[$i * 2]['id']);
            $locationTo = Location::find($randomLocations[$i * 2 + 1]['id']);
            $km = Route::getDistanceBetween($locationFrom, $locationTo);

            $deletedMarket->location_from = $randomLocations[$i * 2]['id'];
            $deletedMarket->location_to = $randomLocations[$i * 2 + 1]['id'];
            $deletedMarket->price = self::frand($cargo->min_price, $cargo->max_price) * $km;
            $deletedMarket->amount = $cargo->weight * random_int(5, 15);
            $deletedMarket->km = $km;
            $deletedMarket->deleted_at = null;
            $deletedMarket->expires_at = $expiresAtCarbon;
            $deletedMarket->save();

            QueueJobUtility::dispatch(new DeleteMarket($deletedMarket), $expiresAtCarbon);
        }

        activity()->enableLogging();
    }

    /**
     * @param $min
     * @param $max
     * @param int $decimals
     * @return float|int
     */
    public static function frand($min, $max, $decimals = 0) {
        $scale = pow(10, $decimals);
        return mt_rand($min * $scale, $max * $scale) / $scale;
    }
}
