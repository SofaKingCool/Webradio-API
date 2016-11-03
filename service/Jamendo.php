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
        $results = [];

        foreach ($body->results as $song) {
            $results[] = [
                "id" => $song->id,
                "title" => $song->name . " - " . $song->artist_name,
                "duration" => $song->duration,
            ];
        }

        return $results;
    }

    public function url($id)
    {
        $url = "https://api.jamendo.com/v3.0/tracks/file?";

        $url .= http_build_query([
            "client_id" => $this->client_id,
            "audioformat" => "mp32",
            "action" => "stream",
            "id" => $id,
        ]);

        $headers = get_headers($url, 1);
        $httpStatus = $headers[0];

        if (!isset($headers["Location"]) || stripos($httpStatus, "302 Found") === false) {
            return false;
        }

        return $headers["Location"];
    }
}
