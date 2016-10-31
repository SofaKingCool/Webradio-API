<?php

class service
{
    public static function exists()
    {
        // Use services array from config.php
        global $services;

        $service = self::name();

        // Check if we support the requested service
        if (!in_array($service, $services, true)) {
            return false;
        }

        // Build the path to the service class file
        $filepath = __DIR__ . "/../service/" . $service . ".php";

        if (!is_readable($filepath)) {
            return false;
        }

        include $filepath;

        return class_exists($service, false);
    }

    public static function get()
    {
        $service = self::name();
        return new $service();
    }

    public static function name()
    {
        return input("service");
    }
}
