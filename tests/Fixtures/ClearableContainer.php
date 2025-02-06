<?php

namespace Tests\Fixtures;

use Webman\Container;

class ClearableContainer extends Container
{
    public function clear()
    {
        $this->instances = [];
    }
}