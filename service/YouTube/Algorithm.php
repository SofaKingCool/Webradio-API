<?php
namespace YouTube;

class Algorithm
{
    private $methods;

    public function __construct($pattern)
    {
        $this->apply($pattern);
    }

    public function apply($pattern)
    {
        // Reverse;Splice:44;Swap:2
        $this->methods = explode(";", $pattern);

        // Convert methods to arrays
        foreach ($this->methods as &$method) {
            if (strpos($method, ":") !== false) {
                $method = explode(":", $method);
            }
            else {
                $method = array($method);
            }
        }

        // Destroy reference variable
        unset($method);
    }

    public function decode($signature)
    {
        $arrayString = new ArrayString($signature);

        foreach ($this->methods as $method) {
            $name = $method[0];
            $offset = isset($method[1]) ? intval($method[1]) : null;

            if ($name == "Reverse") {
                $arrayString->reverse();
            }
            else if ($name == "Splice") {
                $arrayString->splice($offset);
            }
            else if ($name == "Swap") {
                $arrayString->swap($offset);
            }
        }

        return $arrayString->toString();
    }
}
