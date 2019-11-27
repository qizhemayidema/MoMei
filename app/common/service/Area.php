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
     * @param bool $page    是否分页 true 分 false 否
     * @return \think\Collection|\think\Paginator
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getListByPId($pId = 0,$page = Null)
    {
        $areaModel = new \app\common\model\Area();

        $handler = $areaModel->where(['pid'=>$pId]);

        return $page ? $handler->paginate($page) : $handler->select();

    }

    /**
     * 查询一条
     * @param $id
     * @return array|null|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * $data 2019/11/27 13:53
     */
    public function getFindById($id)
    {
        return (new \app\common\model\Area())->get($id);
    }

    /**
     * 更新数据
     * @param $data
     * @return \app\common\model\Area
     * $data 2019/11/27 14:00
     */
    public function update($data)
    {
        $updateArr = [
            'level_id' => $data['level_id'],
            'level_value' => $data['level_value'],
            'level_sort' => $data['level_sort'],
            'is_hot' => $data['is_hot']
        ];

        return (new \app\common\model\Area())->modify($data['id'],$updateArr);

    }
}