<?php

namespace Shetabit\ResponseCache;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Http\Events\RequestHandled;

class MakeCache
{


    public static function clearCache()
    {
        $files = new Filesystem;
        foreach ($files->files(storage_path('framework/cache')) as $file) {
            if (preg_match('/facade-page.*\.php$/', $file)) {
                $files->delete($file);
            }
        }
    }
    public static function handle(RequestHandled $requestHandled)
    {
        $request = $requestHandled->request;
        if (!$request->isMethod('GET') ||  app()->runningInConsole()) {
            static::clearCache();

            return;
        }
        $cachePath = storage_path('framework/cache');
        $response = $requestHandled->response;
        static::pushToCacheFile($response, $cachePath);
    }

    public static function pushToCacheFile($response, $cachePath){
        $nameOfFile = 'facade-page-' . sha1(random_bytes(100));
        $fileFullPath = $cachePath . '/' . $nameOfFile . '.php';
        $filePath = $nameOfFile . '.php';
        touch($fileFullPath);
        $data = (object)[
            'headers' => $response->headers->all(),
            'content' => $response->getContent()
        ];
        $data = json_encode($data);
        file_put_contents($fileFullPath, $data);

       static::pushToMap($filePath);
    }

    public static function PushToMap($filePath){
        $mapPath = storage_path('framework/cache/facade-page-map.php');
        try {
            $cacheMap = file_get_contents($mapPath);
        } catch (\ErrorException $exception) {
            touch($mapPath);
            file_put_contents($mapPath, '{}');
            $cacheMap = file_get_contents($mapPath);
        }
        $data = json_decode($cacheMap , true);
        $data[static::getUrl()] = $filePath;
        $data = json_encode($data);
        file_put_contents($mapPath , $data);
    }

    public static function getUrl()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

}
