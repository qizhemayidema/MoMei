<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/26
 * Time: 15:43
 */

namespace app\common\typeCode\aUser;


use app\common\typeCode\AUserImpl;

class Yuan implements AUserImpl
{
    private $userType = 2;

    public function getUserType(): int
    {
        return $this->userType;
    }
}