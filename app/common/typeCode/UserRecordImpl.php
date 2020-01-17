<?php


namespace app\common\typeCode;


interface UserRecordImpl extends BaseImpl
{
    public function getType() : int;
}