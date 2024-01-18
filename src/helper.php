<?php

use WebmanTech\AmisAdmin\Amis;
use Webman\Http\Response;

// TODO 移除

if (!function_exists('amis')) {
    /**
     * @return Amis
     */
    function amis(): Amis
    {
        return \WebmanTech\AmisAdmin\Webman\AmisFacade::amis();
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
        return amis()->json($data, $msg, $extra);
    }
}
