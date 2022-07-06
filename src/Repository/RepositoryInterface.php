<?php

namespace Kriss\WebmanAmisAdmin\Repository;

interface RepositoryInterface
{
    /**
     * 分页数据列表
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
     * @param array $attributes
     * @return void
     */
    public function create(array $attributes): void;

    /**
     * 更新
     * @param array $attributes
     * @param $id
     * @return void
     */
    public function update(array $attributes, $id): void;

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
}
