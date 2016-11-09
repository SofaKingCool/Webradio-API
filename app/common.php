<?php
/*
 * This script contains generic functions, which aren't big enough to get
 * packed into their own class.
 */

function input($name) {
    return $_GET[$name] ?? null;
}

function error($errno, $error) {
    echo json_encode([ "error" => $error, "errno" => $errno ]);
    exit;
}

function redirect($url, $statusCode) {
    header("Location: " . $url, true, $statusCode);
    exit;
}

// Note: Store URL in a history array to avoid calling this functions
// multiple times on the same URL wasting response time
function alive($url) {
    $headers = get_headers($url, 1);

    if (!$headers) {
        return false;
    }

    $httpStatus = $headers[0];

    return stripos($httpStatus, "200 OK") !== false;
}
