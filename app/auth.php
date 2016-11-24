<?php

class auth
{
    public static function guest()
    {
        // Get the requested method from script name
        $method = basename($_SERVER["SCRIPT_FILENAME"], ".php");

        // Use keys array from config.php
        $keys = false;

        if ($method == "search" || $method == "cleanup") {
            global $searchKeys;
            $keys = $searchKeys;
        }
        else if ($method == "stream") {
            global $streamKeys;
            $keys = $streamKeys;
        }

        // Check for default configuration
        if (in_array("PLEASE-CHANGE-THIS-RANDOM-KEY", $keys, true)) {
            throw new Exception("Found default access key in configuration");
        }

        // Search for GET field "key" in keys
        return !in_array(input("key"), $keys, true);
    }
}
