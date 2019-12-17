<?php
declare (strict_types = 1);

namespace app\common\model;

use app\common\model\impl\BasicImpl;
use app\common\model\impl\ShowImpl;
use think\Model;

/**
 * @mixin think\Model
 */
class CinemaProduct extends Model implements BasicImpl,ShowImpl
{
    /**
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        // TODO: Implement get() method.
    }

    /**
     * @param array $data
     * @return int
     */
    public function add(Array $data): int
    {
        // TODO: Implement add() method.
    }

    /**
     * @param $id
     * @param $data
     * @return mixed
     */
    public function modify($id, $data)
    {
        // TODO: Implement modify() method.
    }

    /**
     * @param $id
     * @return mixed
     */
    public function rm($id)
    {
        // TODO: Implement rm() method.
    }

    /**
     * @param $id
     * @return mixed
     */
    public function softDelete($id)
    {
        // TODO: Implement softDelete() method.
    }

    /**
     * @param string $alias
     * @return mixed
     */
    public function receptionShowData(string $alias = '')
    {
        $alias = $alias ? $alias . '.' : '';

        return $this->where([
            $alias . 'status' => 1,
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
