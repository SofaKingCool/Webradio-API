<?php

class YouTube
{
    private $api_key;

    public function __construct()
    {
        $this->api_key = config::get("api_key");
    }

    public function search($query)
    {
        $url = "https://www.googleapis.com/youtube/v3/search?";

        $url .= http_build_query([
            "q" => $query,
            "key" => $this->api_key,
            "type" => "video",
            "part" => "id, snippet",
            "maxResults" => 30,
            "order" => "viewCount"
        ]);

        $body = json_decode(file_get_contents($url));
        $results = [];

        foreach ($body->items as $song) {
            $results[] = [
                "id" => $song->id->videoId,
                "title" => $song->snippet->title,
            ];
        }

        return $results;
    }
}
