<?php

class loadCache {
    protected $url;

    protected $pathMap = __DIR__ . "/../../storage/framework/cache/facade-page-map.php";

    protected $pathFileToLoad;

    public function __construct()
    {
        $this->url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

//when file created
    function readInMapFile()
    {
        $json = file_get_contents($this->pathMap); //read
        $data = json_decode($json , true); //decode
        if (!isset($data[$this->url])) {
            return;
        }
        $pageFile = $data[$this->url];
        $data = file_get_contents(__DIR__."/../../storage/framework/cache/". $pageFile); // read
        if (!$data) {
            return;
        }
        $data = json_decode($data);
        foreach ($data->headers as $key => $value) {
            header($key.':'.$value[0]);
       }
        //echo body site
        echo $data->content;
        die();
    }
}

$a = new loadcache;
$a->readInMapFile();
