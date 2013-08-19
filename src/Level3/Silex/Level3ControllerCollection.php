<?php

namespace Level3\Silex;

use Silex\ControllerCollection;

class Level3ControllerCollection extends ControllerCollection
{
    public function options($pattern, $to)
    {
        return $this->match($pattern, $to)->method('OPTIONS');
    }
}