<?php

use support\Container;
use Webman\Http\Response;
use WebmanTech\AmisAdmin\Amis;

if (!function_exists('amis')) {
    /**
     * @return Amis
     */
    function amis(): Amis
    {
        /** @phpstan-ignore-next-line */
        return Container::get(Amis::class);
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
