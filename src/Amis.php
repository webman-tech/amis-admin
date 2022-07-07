<?php

namespace Kriss\WebmanAmisAdmin;

use Kriss\WebmanAmisAdmin\Exceptions\ValidationException;
use Kriss\WebmanAmisAdmin\Helper\ArrayHelper;
use Kriss\WebmanAmisAdmin\Helper\ConfigHelper;
use support\view\Raw;
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
        if ($exceptionHandler = ConfigHelper::get('amis.exception_handler')) {
            try {
                $result = $exceptionHandler($this, $e, $extraInfo);
                if ($result instanceof Response) {
                    return $result;
                }
                if (is_string($result)) {
                    return $this->response([], $result);
                }
                if (is_array($result)) {
                    return $this->response($result);
                }
                if ($result instanceof Throwable) {
                    $e = $result;
                }
            } catch (Throwable $newException) {
                $e = $newException;
            }
        }

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

    /**
     * 渲染 app 多页面结构
     * @param array $schema
     * @return string
     * @throws Throwable
     */
    public function renderApp(array $schema = [])
    {
        $data = ConfigHelper::get('amis.app', [
            'view' => 'amis-app',
            'view_path' => '../vendor/kriss/webman-amis-admin/src', // 相对 app 目录
        ]);
        $data = ArrayHelper::merge($data, [
            'assets' => ConfigHelper::get('amis.assets', []),
            'amisJSON' => $schema,
        ]);
        return Raw::render($data['view'], $data, $data['view_path']);
    }

    /**
     * 渲染单页面
     * @param string $title
     * @param array $schema
     * @return string
     * @throws Throwable
     */
    public function renderPage(string $title, array $schema = [])
    {
        $data = ConfigHelper::get('amis.page', [
            'view' => 'amis-page',
            'view_path' => '../vendor/kriss/webman-amis-admin/src', // 相对 app 目录
        ]);
        $data = ArrayHelper::merge($data, [
            'assets' => ConfigHelper::get('amis.assets', []),
            'amisJSON' => $schema,
            'title' => $title,
        ]);

        return Raw::render($data['view'], $data, $data['view_path']);
    }
}