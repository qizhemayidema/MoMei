<?php


namespace app\common\typeCode\record;


use app\common\typeCode\UserRecordImpl;

class Collect implements UserRecordImpl
{
    private $type = 1;   //收藏
    public function getType(): int
    {
        // TODO: Implement getType() method.
        return $this->type;
    }

}