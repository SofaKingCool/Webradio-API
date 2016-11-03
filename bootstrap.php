<?php

// Include app configuration
require __DIR__ . "/config.php";

// Include helpers and library files
require __DIR__ . "/app/panic.php";
require __DIR__ . "/app/common.php";
require __DIR__ . "/app/errors.php";
require __DIR__ . "/app/auth.php";
require __DIR__ . "/app/service.php";
require __DIR__ . "/app/config.php";
require __DIR__ . "/app/cache.php";

// Force content type
header("Content-Type: application/json; charset=UTF-8");

// Force encoding to UTF-8
mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");
mb_http_output("UTF-8");
mb_http_input("UTF-8");

// Authentication
if (auth::guest()) {
    error(ERRNO_NO_AUTH, "Unauthorized");
}

// Service check
if (!service::exists()) {
    error(ERRNO_SERVICE_NOT_FOUND, "Service Unavailable");
}
