<?php

class Jamendo
{
    private $client_id;

    public function __construct()
    {
        $this->client_id = config::get("client_id");
    }

    public function search($query)
    {
        $url = "https://api.jamendo.com/v3.0/tracks/?";

        $url .= http_build_query([
            "search" => $query,
            "client_id" => $this->client_id,
            "format" => "json",
            "boost" => "listens_total",
            "limit" => 30,
        ]);

        $body = json_decode(file_get_contents($url));

        foreach ($body->results as $song) {
            $results[] = [
                "id" => $song->id,
                "title" => $song->name . " - " . $song->artist_name,
                "duration" => $song->duration,
            ];
        }

        return $results;
    }
}
