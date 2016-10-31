<?php

// Debug toggle
$debug_mode = false;

// List of keys you grant access to the entire API
$keys = [
    "PLEASE-CHANGE-THIS-RANDOM-KEY",
];

// List of services you support in this application
$services = [
    "YouTube",
    "Soundcloud",
    "MyFreeMP3",
    "Jamendo",
    "MP3Library",
];

// Per-service configuration
$configs = [
    "YouTube" => [
        // https://developers.google.com/youtube/registering_an_application#Create_API_Keys
        "api_key" => "<your api key>",
    ],
    "Soundcloud" => [
        // // http://soundcloud.com/you/apps
        "client_id" => "<your client id>",
    ],
    "Jamendo" => [
        // https://devportal.jamendo.com/admin/applications
        "client_id" => "<your client id>"
    ],
];
