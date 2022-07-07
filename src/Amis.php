<?php

namespace Kriss\WebmanAmisAdmin;

use Kriss\WebmanAmisAdmin\Exceptions\ValidationException;
use Throwable;
use Webman\Http\Response;

class Amis
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
            'data' => $data ?? '{}',
        ], $extra);

        return json($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param Throwable $e
     * @param array $extraInfo
     * @return Response
     */
    public function handleException(Throwable $e, array $extraInfo = []): Response
    {
        // TODO config 处理
        if ($e instanceof ValidationException) {
            // 服务端验证的返回形式参考
            // https://aisuda.bce.baidu.com/amis/zh-CN/components/form/formitem#%E9%80%9A%E8%BF%87%E8%A1%A8%E5%8D%95%E6%8F%90%E4%BA%A4%E6%8E%A5%E5%8F%A3
            return $this->response([], '', [
                'errors' => $e->errors,
                'status' => 422,
            ]);
        }

        return $this->response([], $e->getMessage());
    }
}