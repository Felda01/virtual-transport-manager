<?php
namespace App\Utilities;


use Illuminate\Http\Request;

/**
 * Class ImageProxyRequest
 * @package App\Utilities
 */
class ImageProxyRequest
{
    /**
     * @param array $params
     * @return mixed
     */
    public static function createImage(array $params)
    {
        $proxy = Request::create(\App\Http\Controllers\AdminController::CREATE_IMAGE_URL, 'post', $params);
        $proxy->headers->set('Access-Control-Allow-Origin', '*');
        $proxy->headers->set('Access-Control-Allow-Methods', '*');
        $proxy->headers->set('Access-Control-Allow-Headers', '*');

        $response = app()->handle($proxy);
        $response->headers->set('Access-Control-Allow-Origin', config('services.frontend.url'));
        $response->headers->set('Access-Control-Allow-Methods', '*');
        $response->headers->set('Access-Control-Allow-Headers', '*');

        if (!$response->isSuccessful()) {
            abort($response);
        }
        return $response->getContent();
    }
}
