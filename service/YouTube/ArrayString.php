<?php
namespace YouTube;

class ArrayString
{
    private $array;

    public function __construct($string)
    {
        $this->array = str_split($string);
    }

    public function reverse()
    {
        $this->array = array_reverse($this->array);
    }

    public function splice($offset)
    {
        array_splice($this->array, 0, $offset);
    }

    public function swap($offset)
    {
        $tmp = $this->array[0];
        $this->array[0] = $this->array[$offset % sizeof($this->array)];
        $this->array[$offset] = $tmp;
    }

    public function toString()
    {
        return implode("", $this->array);
    }
}
