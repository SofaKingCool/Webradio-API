<?php

// Include bootstrapping script
require __DIR__ . "/../bootstrap.php";

// Read identicator from URL
$id = input("id");

if (!$id || mb_strlen($id) < 3) {
    error(ERRNO_BAD_ID, "Unsatisfied Identicator Condition");
}

// Serve results from cache
$cache = new cache("stream", $id);

if ($cache->has()) {
    $url = $cache->get();
    redirect($url, 301);
}

// Produce an instance of the service class
$service = service::get();

// Get the destination URL to the audio
$url = $service->url($id);

if (!$url) {
    header("HTTP/1.0 410 Gone");
    exit;
}

// Save output in cache
$cache->save($url);

// Redirect client to the URL
redirect($url, 301);
