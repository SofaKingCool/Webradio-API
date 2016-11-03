<?php

class MP3Library
{
    public function search($query)
    {
        $number = rand(1, 8);
        $url = "http://cn{$number}.mp3li.org/audio.search/?";

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

    public function url($hash)
    {
        $number = rand(1, 8);
        $url = "http://cn{$number}.mp3li.org/audio.getlinks/?";

        $url .= http_build_query([
            "hash" => $hash,
            "format" => "json",
        ]);

        $body = json_decode(file_get_contents($url));
        $direct = [];
        $ported = [];

        // There are two kind types: "d" (direct?) and "p" (ported)?
        foreach ($body->result as $source) {
            if ($source->kind != "d") {
                $ported[] = $source->url;
            } else {
                $direct[] = $source->url;
            }
        }

        // Try to find a healthy direct link
        foreach ($direct as $url) {
            if (alive($url)) {
                return $url;
            }
        }

        // Otherwise try to find a working ported link
        foreach ($ported as $url) {
            $body = json_decode(file_get_contents($url));

            if ($body && isset($body->result) && alive($body->result->url)) {
                return $body->result->url;
            }
        }

        return false;
    }
}
