<?php

// Include bootstrapping script
require __DIR__ . "/../bootstrap.php";

// Read search query from URL
$query = input("query");

if (!$query || mb_strlen($query) < 3) {
    error(ERRNO_BAD_QUERY, "Unsatisfied Query Condition");
}

// Produce an instance of the service class
$service = service::get();

// Search for songs via service
$songs = $service->search($query);

// Present songs to client
echo json_encode($songs);
