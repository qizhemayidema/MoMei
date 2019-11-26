<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/26
 * Time: 15:45
 */

namespace app\common\typeCode\aUser;


use app\common\typeCode\AUserImpl;

class Ying implements AUserImpl
{
    private $userType = 1;

    public function getUserType(): int
    {
        return $this->userType;
    }

}