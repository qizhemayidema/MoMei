<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/12/4
 * Time: 13:37
 */

namespace app\common\model;


use app\common\model\impl\BasicImpl;
use think\Model;

class BoxOfficeStatistics extends Model implements BasicImpl
{
    public function get($id)
    {
        // TODO: Implement get() method.
    }

    public function add(Array $data): int
    {
        // TODO: Implement add() method.
        return $this->insert($data);
    }

    public function modify($id, $data)
    {
        // TODO: Implement modify() method.
    }

    public function rm($id)
    {
        // TODO: Implement rm() method.
    }

    public function softDelete($id)
    {
        // TODO: Implement softDelete() method.
    }

}