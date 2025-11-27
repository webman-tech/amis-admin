<?php

use Webman\Http\Response;
use WebmanTech\AmisAdmin\Amis;
use WebmanTech\CommonUtils\Container;

if (!function_exists('amis')) {
    /**
     * @return Amis
     */
    function amis(): Amis
    {
        /** @phpstan-ignore-next-line */
        return Container::getCurrent()->get(Amis::class);
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
