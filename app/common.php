<?php
/*
 * This script contains generic functions, which aren't big enough to get
 * packed into their own class.
 */

function input($name) {
    return isset($_GET[$name]) ? $_GET[$name] : null;
}

function error($errno, $error) {
    echo json_encode([ "error" => $error, "errno" => $errno ]);
    exit;
}

function redirect($url, $statusCode) {
    header("Location: " . $url, true, $statusCode);
    exit;
}

function alive($url) {
    static $history = [];

    if (isset($history[$url])) {
        return $history[$url];
    }

    $headers = get_headers($url, 1);

    if (!$headers) {
        return false;
    }

    $httpStatus = $headers[0];
    $alive = stripos($httpStatus, "200 OK") !== false;
    $history[$url] = $alive;

    return $alive;
}
