<?php

class config
{
    public static function get($key)
    {
        // Use configs array from config.php
        global $configs;

        // Use the active service name for the first dimension
        $service = service::name();

        // Check if the service has a configuration array
        if (!isset($configs[$service])) {
            return null;
        }

        $config = $configs[$service];

        return isset($config[$key]) ? $config[$key] : null;
    }
}
