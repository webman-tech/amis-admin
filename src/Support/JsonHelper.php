<?php

namespace WebmanTech\AmisAdmin\Support;

class JsonHelper
{
    /**
     * 从文件中读取 json
     * @param string $jsonFile
     * @return array
     */
    public static function getFromJsonFile(string $jsonFile): array
    {
        return json_decode(file_get_contents($jsonFile), true);
    }

    /**
     * encode
     * @param array $data
     * @param int $flags
     * @return false|string
     */
    public static function encode(array $data, int $flags = JSON_UNESCAPED_UNICODE)
    {
        return json_encode($data, $flags);
    }
}