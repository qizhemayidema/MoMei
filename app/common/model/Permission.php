<?php
declare (strict_types = 1);

namespace app\common\model;

use app\common\model\impl\BasicImpl;
use app\common\tool\Cache;
use think\Model;

class Permission extends Model implements BasicImpl
{
    public function getList(\app\common\typeCode\Base $base,$start = 0,$length = 10)
    {
        // TODO: Implement getList() method.
        $type = $base->getPermissionType();
        $cache = new Cache($base);
    }

    public function backgroundShowData(string $alias)
    {
        // TODO: Implement backgroundShowData() method.
    }

    public function receptionShowData(string $alias)
    {
        // TODO: Implement receptionShowData() method.
    }
}
