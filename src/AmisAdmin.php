<?php

namespace Kriss\WebmanAmisAdmin;

use Webman\Http\Response;

class AmisAdmin
{
    /**
     * @link https://aisuda.bce.baidu.com/amis/zh-CN/docs/types/api?page=1#%E6%8E%A5%E5%8F%A3%E8%BF%94%E5%9B%9E%E6%A0%BC%E5%BC%8F-%E9%87%8D%E8%A6%81-
     * @param array $data
     * @param string $msg
     * @param array $extra
     * @return Response
     */
    public function response(array $data, string $msg = '', array $extra = []): Response
    {
        $data = array_merge([
            'status' => $msg ? 1 : 0,
            'msg' => $msg,
            'data' => $data,
        ], $extra);

        return json($data, JSON_UNESCAPED_UNICODE);
    }
}