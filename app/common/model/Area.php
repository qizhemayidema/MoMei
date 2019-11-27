<?php
declare (strict_types = 1);

namespace app\common\model;

use app\common\model\impl\BasicImpl;
use think\Model;

class Area extends Model implements BasicImpl
{
    public function get($id)
    {
        return $this->where(['id'=>$id])->find();
    }

    public function getList(\app\common\typeCode\BaseImpl $base, $start, $length)
    {
    }

    public function add(Array $data): int
    {
    }

    public function modify($id, $data)
    {
        return $this->where(['id'=>$id])->update($data);
    }

    public function rm($id)
    {
    }

    public function softDelete($id)
    {
    }
}
