<?php
abstract class Registry_Abstract
{
    abstract protected function get($key);
    //get stored object.
    abstract protected function set($key,$val);
    abstract protected function exists($key);
}
?>
