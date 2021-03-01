
first :
require_once __DIR__ . '/../app/caches/LoadCache.php';

create ServiceProvider => CacheServiceProvider.php
set to provider app config
```
    public function boot()
    {
        Event::listen(RequestHandled::class, [MakeCache::class, 'handle']);
    }
````
 ================

 create Listeners =>  MakeCache.php

``````
public function handle(RequestHandled $requestHandled)
    {
        $request = $requestHandled->request;
        if (!$request->isMethod('GET') ||  app()->runningInConsole()) {
            return;
        }
        $cachePath = storage_path('framework/cache');
        $response = $requestHandled->response;
        $this->pushToCacheFile($response, $cachePath);
    }

    public function pushToCacheFile($response, $cachePath){
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

        $this->pushToMap($filePath);
    }

    public function PushToMap($filePath){
        $mapPath = storage_path('framework/cache/facade-page-map.php');
        try {
            $cacheMap = file_get_contents($mapPath);
        } catch (\ErrorException $exception) {
            touch($mapPath);
            file_put_contents($mapPath, '{}');
            $cacheMap = file_get_contents($mapPath);
        }
        $data = json_decode($cacheMap , true);
        $data[$this->getUrl()] = $filePath;
        $data = json_encode($data);
        file_put_contents($mapPath , $data);
    }

    public function getUrl()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
`````````````
