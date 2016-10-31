<?php

class MP3Library
{
    public function search($query)
    {
        // http://cn{number}.mp3li.org
        $url = "http://cn7.mp3li.org/audio.search/?";

        $url .= http_build_query([
            "query" => $query,
            "format" => "json",
        ]);

        $body = json_decode(file_get_contents($url));
        $results = [];

        foreach ($body->result->items as $song) {
            $results[] = [
                "id" => $song->hash,
                "title" => $song->track . " - " . $song->artist,
                "duration" => $song->length,
            ];
        }

        return $results;
    }

    /*
        http://cn7.mp3li.org/audio.getlinks/?hash=1b4c5e994b4952166895651d32bb7941&format=json
    */
}
