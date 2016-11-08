<?php

class YouTube
{
    const supported_itags = array(18, 22, 37, 38);

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

    public function url($id)
    {
        $url = "http://www.youtube.com/get_video_info?";
        
        $url .= http_build_query([
            "video_id" => $id,
            "asv" => "3",
            "el" => "detailpage",
        ]);
        
        $video_info = array();
        parse_str(file_get_contents($url), $video_info);

        if (!isset($video_info["url_encoded_fmt_stream_map"])) {
            return false;
        }
        
        $url_encoded_fmt_stream_map = $video_info["url_encoded_fmt_stream_map"];
        $url_encoded_fmt_streams = explode(",", $url_encoded_fmt_stream_map);

        foreach ($url_encoded_fmt_streams as $encoded_stream) {
            $stream = array();
            parse_str($encoded_stream, $stream);
            
            $mime = strtok($stream["type"], ";");
            $itag = intval($stream["itag"]);
            
            if ($mime == "video/mp4" && in_array($itag, self::supported_itags) && !isset($stream["s"])) {
                $sources[] = array(
                    "url" => urldecode($stream["url"]),
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
