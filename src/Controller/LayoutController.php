<?php

namespace Kriss\WebmanAmisAdmin\Controller;

use support\view\Raw;

class LayoutController
{
    public function index()
    {
        $data = config('plugin.kriss.webman-amis-admin.amis.layout');
        return Raw::render($data['view'], $data, $data['view_path']);
    }
}
