<?php

// Include configuration prematurely
require __DIR__ . "/../config.php";

// Hack to bypass service check in bootstrapper
global $services;
$_GET["service"] = $services[0];

// Include bootstrapping script
require __DIR__ . "/../bootstrap.php";

class Cleaner
{
    private $maxFileAge;
    private $files = array();

    public function __construct($directory, $maxFileAge)
    {
        $this->maxFileAge = $maxFileAge;
        $this->investigate($directory);
    }

    private function investigate($directory)
    {
        $handle = opendir($directory);

        if ($handle) {
            while ($filename = readdir($handle)) {
                if (strpos($filename, ".") === 0) {
                    continue;
                }

                $filepath = realpath($directory . DIRECTORY_SEPARATOR . $filename);

                if (is_dir($filepath)) {
                    $this->investigate($filepath);
                }
                else if ((time() - filemtime($filepath)) >= $this->maxFileAge) {
                    $this->files[] = $filepath;
                }
            }

            closedir($handle);
        }
    }

    public function clean()
    {
        foreach ($this->files as $filepath) {
            unlink($filepath);
        }
    }
}

// Use cache file-lifetime variables from config
global $cacheMaxSearchAge;
global $cacheMaxStreamAge;
global $cacheMaxMiscAge;

$directories = [
    "search" => $cacheMaxSearchAge,
    "stream" => $cacheMaxStreamAge,
    "misc" => $cacheMaxMiscAge,
];

// Clean up every storage directory
foreach ($directories as $directory => $maxAge) {
    $cleaner = new Cleaner(__DIR__ . "/../storage/{$directory}", $maxAge);
    $cleaner->clean();
}
