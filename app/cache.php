<?php

class cache
{
    const directory = __DIR__ . "/../storage/";

    private $filepath;
    private $maxAge;

    public function __construct($method, $identicator)
    {
        // Take maximum cache age from configuration
        if ($method == "search") {
            global $cacheMaxSearchAge;
            $this->maxAge = $cacheMaxSearchAge;
        }
        else if ($method == "stream") {
            global $cacheMaxStreamAge;
            $this->maxAge = $cacheMaxStreamAge;
        }
        else if ($method == "misc") {
            global $cacheMaxMiscAge;
            $this->maxAge = $cacheMaxMiscAge;
        }

        // Create directory for service in cache storage
        $service = service::name();

        $directory = self::directory . $method . "/" . $service . "/";

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Build the path to the cache file
        $identicator = md5(mb_strtolower($identicator));

        $this->filepath = $directory . $identicator;
    }

    public function has()
    {
        if (!file_exists($this->filepath)) {
            return false;
        }

        if (!filesize($this->filepath)) {
            return false;
        }

        return (time() - filemtime($this->filepath)) < $this->maxAge;
    }

    public function serve()
    {
        echo $this->get();
        exit;
    }

    public function get()
    {
        return file_get_contents($this->filepath);
    }

    public function save($content)
    {
        file_put_contents($this->filepath, $content);
    }
}
