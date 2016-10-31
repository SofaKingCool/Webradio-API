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
