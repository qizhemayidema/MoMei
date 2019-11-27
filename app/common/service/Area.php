<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/26
 * Time: 18:15
 */

namespace app\common\service;


class Area
{
    /**
     * @param int $pId  父级id
     * @param bool $page    是否分页 传数字 分 false 否
     * @return \think\Collection|\think\Paginator
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getListByPId($pId = 0,$page = false)
    {
        $areaModel = new \app\common\model\Area();

        $handler = $areaModel->where(['pid'=>$pId]);

        return $page ? $handler->paginate($page) : $handler->select();

    }
}