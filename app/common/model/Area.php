<?php
declare (strict_types = 1);

namespace app\common\model;

use app\common\model\impl\BasicImpl;
use think\Model;

class Area extends Model implements BasicImpl
{
    public function get($id)
    {

    }

    public function getList(\app\common\typeCode\BaseImpl $base, $start, $length)
    {
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
}
