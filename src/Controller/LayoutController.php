<?php

namespace Kriss\WebmanAmisAdmin\Controller;

use Kriss\WebmanAmisAdmin\Helper\ConfigHelper;
use support\view\Raw;

class LayoutController
{
    public function index()
    {
        $data = ConfigHelper::get('amis.layout', [
            'view' => 'amis-app',
            'view_path' => '../vendor/kriss/webman-amis-admin/src', // 相对 app 目录
        ]);
        return Raw::render($data['view'], $data, $data['view_path']);
    }
}
