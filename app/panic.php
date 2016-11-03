<?php

// Hardcoded here, because errors.php is parsed at a later point of execution
define("ERRNO_INTERNAL_EXCEPTION", 1);

set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        // This error code is not included in error_reporting
        return;
    }

    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function ($exception) {
    // Use debug boolean from config.php
    global $debug_mode;

    if ($debug_mode) {
        echo json_encode([
            "error" => $exception->getMessage(),
            "errno" => $exception->getCode(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine(),
        ], JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode([ "error" => "Out of Order", "errno" => ERRNO_INTERNAL_EXCEPTION ]);
    }

    exit;
});

// Use debug boolean from config.php
global $debug_mode;

if ($debug_mode) {
    // Turn on all error reporting
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
} else {
    // Turn off all error reporting
    error_reporting(0);
    ini_set("display_errors", 0);
}
