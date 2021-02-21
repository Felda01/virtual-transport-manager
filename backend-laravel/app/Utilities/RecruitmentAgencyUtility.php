<?php


namespace App\Utilities;

use App\Jobs\DeleteModel;
use App\Models\Driver;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Storage;

/**
 * Class RecruitmentAgencyUtility
 * @package App\Utilities
 */
class RecruitmentAgencyUtility
{
    /**
     * @param int $count
     */
    public static function newDriver($count = 1)
    {
        $faker = Faker::create();

        activity()->disableLogging();

        for ($i = 0; $i < $count; $i++){
            $gender = array_rand([Driver::GENDER_MALE, Driver::GENDER_FEMALE]);

            $genderText = $gender ? 'female' : 'male';
            $genderImage = $gender ? 'women' : 'men';
            $imageNumber = mt_rand(0, 99);

            $response = file_get_contents("https://randomuser.me/api/portraits/" . $genderImage . "/" . $imageNumber .".jpg");

            $fileName = ImageUtility::saveImage($response, 'jpg');

            $weekInMinutes = 7 * 24 * 60;

            $adr = array_rand(config('constants.adr'));

            $driver = Driver::create([
                'first_name' => $faker->firstName($genderText),
                'last_name' => $faker->lastName,
                'gender' => $gender,
                'status' => -1,
                'image' => Storage::disk('public')->url(ImageUtility::IMAGES_FOLDER . $fileName),
                'company_id' => null,
                'truck_id' => null,
                'location_id' => null,
                'garage_id' => null,
                'salary' => mt_rand(1000, 1400) + ($adr * 400),
                'satisfaction' => 70,
                'preferred_road_trips' => array_rand(config('constants.road_trips')),
                'last_in_garage_at' => null,
                'expires_at' => null,
                'adr' => $adr
            ]);

            QueueJobUtility::dispatch(new DeleteModel($driver), Carbon::parse(GameTimeUtility::gameTimeToRealTime($weekInMinutes)));
        }

        activity()->enableLogging();
    }
}
