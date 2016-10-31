<?php

class auth
{
    public static function guest()
    {
        // Use keys array from config.php
        global $keys;

        // Check for default configuration
        if (in_array("PLEASE-CHANGE-THIS-RANDOM-KEY", $keys, true)) {
            throw new Exception("Found default access key in configuration");
        }

        // Search for GET field "key" in keys
        return !in_array(input("key"), $keys, true);
    }
}
