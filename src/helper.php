<?php

use WebmanTech\AmisAdmin\Amis;
use support\Container;
use Webman\Http\Response;

if (!function_exists('amis_response')) {
    /**
     * @param array $data
     * @param string $msg
     * @param array $extra
     * @return Response
     */
    function amis_response(array $data, string $msg = '', array $extra = [])
    {
        return Container::get(Amis::class)->response($data, $msg, $extra);
    }
}
