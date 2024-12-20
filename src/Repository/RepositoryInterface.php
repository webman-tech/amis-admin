<?php

namespace WebmanTech\AmisAdmin\Repository;

interface RepositoryInterface
{
    public const SCENE_LIST = 'list';
    public const SCENE_DETAIL = 'detail';
    public const SCENE_CREATE = 'create';
    public const SCENE_UPDATE = 'update';

    /**
     * 主键
     * @return string
     */
    public function getPrimaryKey(): string;

    /**
     * 分页数据列表
     * 返回格式参考：https://aisuda.bce.baidu.com/amis/zh-CN/components/crud#%E6%95%B0%E6%8D%AE%E6%BA%90%E6%8E%A5%E5%8F%A3%E6%95%B0%E6%8D%AE%E7%BB%93%E6%9E%84%E8%A6%81%E6%B1%82
     * @param int $page
     * @param int $perPage
     * @param array $search
     * @param array $order
     * @return array
     */
    public function pagination(int $page = 1, int $perPage = 20, array $search = [], array $order = []): array;

    /**
     * 明细
     * @param $id
     * @return array
     */
    public function detail($id): array;

    /**
     * 创建
     * @param array $data
     * @return void
     */
    public function create(array $data): void;

    /**
     * 更新
     * @param array $data
     * @param $id
     * @return void
     */
    public function update(array $data, $id): void;

    /**
     * 删除
     * @param $id
     * @return void
     */
    public function destroy($id): void;

    /**
     * 恢复
     * @param $id
     * @return void
     */
    public function recovery($id): void;

    /**
     * 根据 attribute 获取 label
     * @param string $attribute
     * @return string
     */
    public function getLabel(string $attribute): string;

    /**
     * 根据 attribute 获取 labelRemark
     * @param string $attribute
     * @return string|array|null
     */
    public function getLabelRemark(string $attribute);

    /**
     * 根据 attribute 获取 description
     * @param string $attribute
     * @return string|array|null
     */
    public function getDescription(string $attribute);
}
