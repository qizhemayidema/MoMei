<?php
declare (strict_types = 1);

namespace app\common\model;

use app\common\model\impl\ShowImpl;
use think\Model;

/**
 * @mixin think\Model
 */
class CinemaProductEntity extends Model implements ShowImpl
{
    /**
     * @param string $alias
     * @return mixed
     */
    public function receptionShowData(string $alias = '')
    {
        $alias = $alias ? $alias . '.' : '';

        return $this->where([
            $alias . 'delete_time' => 0,
        ]);
    }

    /**
     * @param string $alias
     * @return mixed
     */
    public function backgroundShowData(string $alias = '')
    {
        $alias = $alias ? $alias . '.' : '';

        return $this->where([
            $alias . 'delete_time' => 0,
        ]);
    }

}
