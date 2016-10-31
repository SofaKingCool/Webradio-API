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
}
