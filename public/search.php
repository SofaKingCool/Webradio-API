<?php

// Include bootstrapping script
require __DIR__ . "/../bootstrap.php";

// Read search query from URL
$query = input("query");

if (!$query || mb_strlen($query) < 3) {
    error(ERRNO_BAD_QUERY, "Unsatisfied Query Condition");
}

// Serve results from cache
$cache = new cache("search", $query);

if ($cache->has()) {
    echo $cache->get();
    exit;
}

// Produce an instance of the service class
$service = service::get();

// Search for songs via service
$songs = $service->search($query);

// Format song list as JSON
$output = json_encode($songs, JSON_UNESCAPED_SLASHES);

// Save output in cache (if there is a song)
if (isset($songs[0])) {
    $cache->save($output);
}

// Present songs to client
echo $output;
