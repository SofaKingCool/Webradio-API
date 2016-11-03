<?php

class Soundcloud
{
    private $client_id;

    public function __construct()
    {
        $this->client_id = config::get("client_id");
    }

    public function search($query)
    {
        $url = "http://api.soundcloud.com/tracks?";

        $url .= http_build_query([
            "q" => $query,
            "client_id" => $this->client_id,
            "filter" => "public",
            "limit" => 30
        ]);

        $songs = json_decode(file_get_contents($url));
        $results = [];

        foreach ($songs as $song) {
            $results[] = [
                "id" => $song->id,
                "title" => $song->title,
                "duration" => floor($song->duration / 1000)
            ];
        }

        return $results;
    }

    public function url($id)
    {
        $url = "http://api.soundcloud.com/tracks/{$id}/stream?";

        $url .= http_build_query([
            "client_id" => $this->client_id,
        ]);

        $headers = get_headers($url, 1);
        $httpStatus = $headers[0];

        if (!isset($headers["Location"]) || stripos($httpStatus, "302 Found") === false) {
            return false;
        }

        return $headers["Location"];
    }
}
