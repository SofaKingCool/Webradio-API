<?php

class MyFreeMP3
{
    public function search($query)
    {
        $base = "http://www.my-free-mp3.com/mp3/{$query}?";
        $results = [];

        for ($p = 1; $p <= 3; ++$p) {
            $url = $base;

            $url .= http_build_query([
                "page" => $p,
                "action" => "ajax"
            ]);

            $body = file_get_contents($url);

            $success = preg_match_all("/data-aid=\"(.+)\".*data-duration=\"(.+)\".*>\s*(.+)(?:\s*mp3)<\/a>/Us", $body, $matches, PREG_SET_ORDER);

            if (!$success) {
                break;
            }

            foreach ($matches as $row) {
                $results[] = [
                    "id" => $row[1],
                    "title" => htmlspecialchars_decode($row[3], ENT_QUOTES | ENT_HTML5),
                    "duration" => intval($row[2]),
                ];
            }
        }

        return $results;
    }
}
