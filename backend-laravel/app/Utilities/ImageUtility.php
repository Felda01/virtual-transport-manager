<?php
namespace App\Utilities;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class ImageUtility
 * @package App\Utilities
 */
class ImageUtility
{
    const BASE64_IMAGE_REGEX_PART = 'data:image\/(?:gif|png|jpeg|bmp|webp)(?:;charset=utf-8)?;base64,(?:[A-Za-z0-9\+\/]{4})*(?:[A-Za-z0-9\+\/]{2}==|[A-Za-z0-9\+\/]{3}=)?';

    const BASE64_IMAGE_REGEX = '^' . self::BASE64_IMAGE_REGEX_PART . '$';

    const IMAGES_FOLDER = 'images/';

    /**
     * @param string $base64Data
     * @param string $oldValue
     * @return bool|string
     */
    static public function changeImageProperty(string $base64Data, string $oldValue)
    {
        if (Str::startsWith($base64Data, ['data:image'])) {
            $oldFileName = Str::of($oldValue)->afterLast('/');

            ImageUtility::removeImage($oldFileName->__toString());

            $fileName = ImageUtility::convertAndSaveBase64Image($base64Data);

            if (!$fileName) {
                return false;
            }
            return Storage::disk('public')->url(ImageUtility::IMAGES_FOLDER . $fileName);
        }
        return $oldValue;
    }

    /**
     * @param string $base64Data
     * @return bool|string
     */
    static public function convertAndSaveBase64Image(string $base64Data)
    {
        preg_match('/^data:image\/(\w+)(?:;charset=utf-8)?;base64,/', $base64Data, $imageExtension);

        $imageData = explode(',', $base64Data);
        $imageData = str_replace(' ', '+', $imageData[1]);
        $imageData = base64_decode($imageData);

        $fileName = Str::random(10) . time() . "." . $imageExtension[1];

        if ($imageData) {
            $result = Storage::disk('public')->put(self::IMAGES_FOLDER . $fileName, $imageData);

            if ($result) {
                return $fileName;
            }
        }
        return false;
    }

    /**
     * @param string $name
     * @return bool
     */
    static public function removeImage(string $name)
    {
        if (Storage::disk('public')->exists(self::IMAGES_FOLDER . $name)) {
            return Storage::disk('public')->delete(self::IMAGES_FOLDER . $name);
        }
        return true;
    }

    /**
     * @return mixed
     */
    static public function getUrlImageRegex()
    {
        return str_replace('.', '\.', str_replace('/', '\/', Storage::disk('public')->url(self::IMAGES_FOLDER)));
    }

    /**
     * @return string
     */
    static public function getBase64ImageOrUrlImageRegex()
    {
        return '^(' . self::BASE64_IMAGE_REGEX . '|' . self::getUrlImageRegex() . '(?:[A-Za-z0-9]{20})\.(?:gif|png|jpeg|bmp|webp))$';
    }
}
