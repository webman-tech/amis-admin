<?php

use WebmanTech\AmisAdmin\Amis;
use Webman\Http\Response;
use WebmanTech\AmisAdmin\Facades\AmisFacade;

if (!function_exists('amis')) {
    /**
     * @deprecated 使用 Facades\AmisFacade:: 代替
     * @return Amis
     */
    function amis(): Amis
    {
        return AmisFacade::instance();
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
        return AmisFacade::response($data, $msg, $extra);
    }
}
