<?php
declare (strict_types = 1);

namespace app\common\model;

use app\common\model\impl\BasicImpl;
use app\common\model\impl\ShowImpl;
use app\common\typeCode\AUserImpl;
use think\Model;

class AUser extends Model implements BasicImpl,ShowImpl
{
    public function get($id)
    {
        return $this->find($id);
    }

    /**
     * 获取列表
     * @param bool $page 是否分页  如果是 则传数量
     * @return array|\think\Paginator
     */
    public function getList($page = false)
    {
        return $page ? $this->paginate($page) : $this->select()->toArray();
    }

    public function add(Array $data): int
    {

    }

    public function modify($id, $data)
    {
        $this->where(['id'=>$id])->update($data);
    }

    public function rm($id)
    {

    }

    public function softDelete($id)
    {
    }

    public function receptionShowData(string $alias = '')
    {
        if ($alias){
            $where = [
                $alias .'.status' => 1,
            ];
        }else{
            $where = [
                'status'    => 1,
            ];
        }

        return $this->where($where);
    }

    public function backgroundShowData(string $alias = '')
    {
        return $this;
    }

    /**
     * 获取列表根据类型
     * @param $type
     * @param bool $page
     * @return array|\think\Paginator
     */
    public function getListByType($type,$page = false)
    {
        $handler = $this->where(['type'=>$type]);

        return $page ? $handler->paginate($page) : $handler->select()->toArray();

    }
}
