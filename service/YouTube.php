<?php

define("__YOUTUBE_DIR__", __DIR__ . "/YouTube/");

require __YOUTUBE_DIR__ . "Decoder.php";
require __YOUTUBE_DIR__ . "ArrayString.php";
require __YOUTUBE_DIR__ . "Algorithm.php";

class YouTube
{
    const supported_itags = array(18, 22, 37, 38);

    private $key;
    private $decoder;

    public function __construct()
    {
        $this->key = config::get("api_key");
        $this->decoder = new YouTube\Decoder();
    }

    public function search($query)
    {
        $url = "https://www.googleapis.com/youtube/v3/search?";

        $url .= http_build_query([
            "q" => $query,
            "key" => $this->key,
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

    public function url($videoId)
    {
        $url = "http://www.youtube.com/get_video_info?";

        $url .= http_build_query([
            "video_id" => $videoId,
            "asv" => "3"
        ]);

        $video_info = array();
        parse_str(file_get_contents($url), $video_info);

        if (!isset($video_info["url_encoded_fmt_stream_map"])) {
            return false;
        }

        $url_encoded_fmt_stream_map = $video_info["url_encoded_fmt_stream_map"];
        $url_encoded_fmt_streams = explode(",", $url_encoded_fmt_stream_map);

        $sources = [];

        foreach ($url_encoded_fmt_streams as $encoded_stream) {
            $stream = array();
            parse_str($encoded_stream, $stream);

            $mime = strtok($stream["type"], ";");
            $itag = intval($stream["itag"]);

            if ($mime == "video/mp4" && in_array($itag, self::supported_itags)) {
                $url = urldecode($stream["url"]);

                if (isset($stream["s"])) {
                    $encoded = $stream["s"];

                    // Note: This decoder can break anytime
                    $signature = $this->decoder->decode($videoId, $url, $encoded);

                    if (!$signature) {
                        continue;
                    }

                    $url = "{$url}&signature={$signature}";
                }

                $sources[] = array(
                    "url" => $url,
                    "itag" => $itag,
                );
            }
        }

        usort($sources, function ($a, $b) {
            return $a["itag"] > $b["itag"];
        });

        foreach ($sources as $source) {
            if (alive($source["url"])) {
                return $source["url"];
            }
        }

        return false;
    }
}
