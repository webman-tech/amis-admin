<?php

namespace WebmanTech\AmisAdmin\PageReader;

use WebmanTech\AmisAdmin\Config;
use WebmanTech\AmisAdmin\Contracts\PageReaderInterface;
use WebmanTech\AmisAdmin\Support\JsonHelper;

class FilePageReader implements PageReaderInterface
{
    private string $dir;
    private string $schemaApiBaseUrl;
    private array $pages;
    private array $fileMap = [];
    private Config $config;

    public function __construct(string $dir, string $schemaApiBaseUrl = '/')
    {
        $this->dir = $dir;
        $this->schemaApiBaseUrl = rtrim($schemaApiBaseUrl, '/') . '/';

        $this->pages = $this->loadDirs($this->dir);
    }

    /**
     * @inheritDoc
     */
    public function getPages(): array
    {
        return $this->pages;
    }

    private array $pageJsonCached = [];

    /**
     * @inheritDoc
     */
    public function getPageJson(string $key): array
    {
        if (!isset($this->pageJsonCached[$key])) {
            $filePath = $this->fileMap[$key] ?? null;
            if ($filePath && file_exists($filePath)) {
                $data = $this->config->solveAmisJSON($filePath, [], $key);
            } else {
                $data = [];
            }
            $this->pageJsonCached[$key] = $data;
        }

        return $this->pageJsonCached[$key];
    }

    /**
     * @inheritDoc
     */
    public function withConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }

    private function loadDirs(string $dir): array
    {
        $pages = [];

        if ($handler = opendir($dir)) {
            while (($file = readdir($handler)) !== false) {
                $isDir = is_dir($dir . DIRECTORY_SEPARATOR . $file);
                if ($this->isIgnoreFile($file, $isDir)) {
                    continue;
                }
                if ($isDir) {
                    $info = $this->parseFile($file, true, $dir);
                    $info['children'] = $this->loadDirs($dir . DIRECTORY_SEPARATOR . $file);
                    $pages[] = $info;
                } else {
                    $filePath = $dir . DIRECTORY_SEPARATOR . $file;
                    $info = $this->parseFile($file, false, $dir);
                    $key = $info['key'] ?? md5($filePath);
                    if (!isset($info['link']) && !isset($info['redirect']) && !isset($info['schemaApi'])) {
                        $info['schemaApi'] = $info['key'];
                    }
                    $this->fileMap[$key] = $filePath;
                    if (isset($info['schemaApi'])) {
                        $info['schemaApi'] = $this->schemaApiBaseUrl . $info['schemaApi'];
                    }
                    $pages[] = $info;
                }
            }
            closedir($handler);
        }
        return $pages;
    }

    /**
     * 是否是忽略文件
     * @param string $filename
     * @param bool $isDir
     * @return bool
     */
    private function isIgnoreFile(string $filename, bool $isDir): bool
    {
        if (in_array($filename, ['.', '..'], true)) {
            return true;
        }
        if (strpos($filename, '.') === 0) {
            return true;
        }
        if (strpos($filename, '__') === 0) {
            return true;
        }
        if (!$isDir) {
            return pathinfo($filename, PATHINFO_EXTENSION) !== 'json';
        }
        return false;
    }

    /**
     * @param string $file 1.菜单 / 01.目录 / 1.首页.json / 1.首页[url=-].json
     * @param bool $isDir
     * @param string $dir 文件所在的路径
     * @return array
     */
    private function parseFile(string $file, bool $isDir, string $dir): array
    {
        $filename = $isDir ? $file : pathinfo($file, PATHINFO_FILENAME);

        // 移除前面的 01. 1. 并得到 label
        $pos = strrpos($filename, '.');
        $label = $filename;
        if ($pos !== false) {
            $tempSort = ltrim(substr($filename, 0, $pos), '0');
            if (is_numeric($tempSort)) {
                $label = substr($filename, $pos + 1);
            }
        }

        if ($isDir) {
            $data = [
                'label' => $label,
                'children' => [],
            ];
        } else {
            $data = [
                'label' => $label,
                'icon' => 'fa fa-circle-o',
                'url' => '/' . md5($label),
            ];
        }

        // 提取 [] 中的参数
        preg_match_all('/\[(.*?)\]/', $data['label'], $matches);

        foreach ($matches[1] as $match) {
            preg_match('/^([a-zA-Z]+)=(.*)/', $match, $arr);
            if (count($arr) === 3) {
                if ($arr[1] === 'link') {
                    // link 从文件中读取
                    $arr[2] = JsonHelper::getFromJsonFile($dir . DIRECTORY_SEPARATOR . $file)['link'] ?? '';
                }
                $data[$arr[1]] = strtr($arr[2], [
                    '\'' => '/', // 用 ' 代替 /
                ]);
                $data['label'] = str_replace("[{$arr[0]}]", '', $data['label']);
            }
        }

        return $data;
    }
}
