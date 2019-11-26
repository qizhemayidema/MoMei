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

    public function getList(\app\common\typeCode\BaseImpl $base, $start = 0,$length = 10)
    {
        if ($base instanceof AUserImpl){}

        $userType = $base->getUserType();

        return $this->where(['type'=>$userType])->limit($start,$length);

    }

    public function add(Array $data): int
    {

    }

    public function modify($id, $data)
    {
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

}
