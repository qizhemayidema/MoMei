<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/23
 * Time: 15:53
 */

namespace app\common\tool;


class User
{
    public function makeSalt()
    {
        //创建盐值
        $salt = md5(mt_rand(10000000000,99999999999));

        return $salt;
    }

    //创建token
    public function makeToken($keyword)
    {
        $rand = mt_rand(1000000,9999999);

        $salt = md5(time() . $this->makeSalt());

        return md5(md5($keyword) . $rand . $salt .  microtime() );
    }
}