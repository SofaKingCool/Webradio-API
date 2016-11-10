<?php
namespace YouTube;

use cache;

class Decoder
{
    private $cache;

    public function __construct()
    {
        $this->cache = new cache("misc", "decoder");
    }

    public function decode($videoId, $baseURL, $encoded)
    {
        // Try to use the existing algorithm in cache before we do heavy text processing
        if ($this->cache->has()) {
            $signature = $this->decodeSignature($encoded);
            $url = "{$baseURL}&signature={$signature}";

            if (alive($url)) {
                return $signature;
            }
        }

        // Get the page content of the JavaScript base file of the YouTube player (contains signature decoder)
        $javascriptFile = $this->getPlayerJavaScriptFile($videoId);

        if (!$javascriptFile) {
            return false;
        }

        // Find the decoder function body in the JavaScript file
        $functionBody = $this->getDecoderFunctionBody($javascriptFile);

        if (!$functionBody) {
            return false;
        }

        $groupName = substr($functionBody, 0, 2);

        // Decipher the function body and generate a correct algorithm pattern
        $pattern = $this->getDecoderAlgorithmPattern($groupName, $functionBody, $javascriptFile);

        if (!$pattern) {
            return false;
        }

        // Swap:10;Reverse:3;Swap:29;Reverse:56;Swap:46;Reverse:46;Swap:10;Splice:2
        $this->cache->save($pattern);

        return $this->decodeSignature($encoded);
    }

    private function decodeSignature($signature)
    {
        $pattern = $this->cache->get();
        return (new Algorithm($pattern))->decode($signature);
    }

    private function getPageWithPlayerURL($videoId)
    {
        // Request a YouTube page with the smallest overhead, which gives you the URL to the player's base.js
        $url = "https://www.youtube.com/embed/{$videoId}";

        return file_get_contents($url);
    }

    private function getPlayerJavaScriptFile($videoId)
    {
        $videoPage = $this->getPageWithPlayerURL($videoId);

        if ($videoPage === false) {
            return false;
        }

        // Use a regular expression to get the player id from the page body
        // <script src="//s.ytimg.com/yts/jsbin/player-{country_code}-{random_id}/base.js" name="player/base"></script>
        $matches = array();
        $success = preg_match("@(player-.+-.+)\/base\.js@Ui", $videoPage, $matches);

        if (!$success) {
            return false;
        }

        // Example: player-de_DE-vflxBseKd
        $baseID = $matches[1];

        $cache = new cache("misc", $baseID);

        if (!$cache->has()) {
            // Note: This URL might change in the future
            $url = "https://s.ytimg.com/yts/jsbin/" . $baseID . "/base.js";

            $body = file_get_contents($url);

            $cache->save($body);
        }

        return $cache->get();
    }

    private function getDecoderFunctionName($javascriptFile)
    {
        // a.set("signature",An(c));
        $matches = array();
        $success = preg_match('@a\.set\("signature",(.+)\(.\)\);@Ui', $javascriptFile, $matches);

        if (!$success) {
            // .signature=An
            preg_match("@\.signature=([a-z]{2})@Ui", $javascriptFile, $matches);

            if (!$success) {
                return false;
            }
        }

        return $matches[1];
    }

    public function getDecoderFunctionBody($javascriptFile)
    {
        // Find the name of the decoder function
        $functionName = $this->getDecoderFunctionName($javascriptFile);

        // Array of JavaScript function name patterns
        $patterns = [
            "function {$functionName}(a){",
            "var {$functionName}=function(a){",
            ",{$functionName}=function(a){",
            "\n{$functionName}=function(a){",
        ];

        // Find the decoder function body by testing each pattern
        $position = false;

        foreach ($patterns as $decoderPattern) {
            if (($position = strpos($javascriptFile, $decoderPattern)) !== false) {
                break;
            }
        }

        // No pattern has matched
        if ($position === false) {
            return false;
        }

        // Increment position number to skip the unrelevant characters
        // An=function(a){a=a.split("");zn.BA(a,2);zn.dm(a,58);zn.dm(a,59);return a.join("")};
        $length = strlen('a=a.split("");');
        $position += strlen($decoderPattern) + $length;

        // Find the end of the decoder function body
        $pattern = ";return a.join";
        $end = strpos($javascriptFile, $pattern, $position);

        // End of the function body not found
        if ($end === false) {
            return false;
        }

        // Cut out the function body and return it to the caller
        // zn.BA(a,2);zn.dm(a,58);zn.dm(a,59)
        return substr($javascriptFile, $position, $end - $position);
    }

    private function getDecoderAlgorithmPattern($groupName, $functionBody, $javascriptFile)
    {
        // var zn={dm:function(a,b){var c=a[0];a[0]=a[b%a.length];a[b]=c},BA:function(a,b){a.splice(0,b)},Cf:function(a){a.reverse()}};
        $success = preg_match("@var " . preg_quote($groupName) . "=\{(.+)\};@Us", $javascriptFile, $matches);

        if (!$success) {
            return false;
        }

        $groupBody = $matches[1];
        $groupFunctions = explode("},", $groupBody);
        $functions = array();

        foreach ($groupFunctions as $fun) {
			list($name, $body) = explode(":", $fun);
            $name = "{$groupName}." . trim($name);

            if (strpos($body, "reverse") !== false) {
                $functions[$name] = 'Reverse';
            }
            else if (strpos($body, "splice") !== false) {
                $functions[$name] = 'Splice';
            }
            else if (strpos($body, "var") !== false) {
                $functions[$name] = 'Swap';
            }
        }

        // "zn.BA" => "Swap"
        $pattern = str_replace(array_keys($functions), array_values($functions), $functionBody);

        // "(a)" => ""
        // "(a,53)" => ":53"
        $pattern = preg_replace_callback("@\(.(?:,(\d+))?\)@",
            function ($matches) {
                if (isset($matches[1])) {
                    return ":{$matches[1]}";
                }
                else {
                    return "";
                }
            },
        $pattern);

        return $pattern;
    }
}
