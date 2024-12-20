<?php

use WebmanTech\AmisAdmin\Amis as AmisStruct;
use Webman\Http\Response;
use WebmanTech\AmisAdmin\Facades\Amis;

if (!function_exists('amis')) {
    /**
     * @return AmisStruct
     */
    function amis(): AmisStruct
    {
        return Amis::instance();
    }
}

if (!function_exists('amis_response')) {
    /**
     * @param array $data
     * @param string $msg
     * @param array $extra
     * @return Response
     */
    function amis_response(array $data, string $msg = '', array $extra = [])
    {
        return amis()->response($data, $msg, $extra);
    }
}
