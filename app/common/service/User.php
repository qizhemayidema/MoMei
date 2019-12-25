<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/24
 * Time: 14:48
 */

namespace app\common\service;

use app\common\model\User as UserModel;

class User
{

    public function insert($data)
    {
        $tool = (new \app\common\tool\User());

        $salt = $tool->makeSalt();

        $insert = [
            'phone' => $data['phone'],
            'password' => $tool->makePassword($salt,$data['password']),
            'salt'  => $salt,
            'token' => $tool->makeToken($data['password'].$salt),
            'create_time' => time(),
        ];

        $model = new \app\common\model\User();

        $model->insert($insert);

        return $model->getLastInsID();
    }

    public function login($phone,$pwd)
    {
        $model = new \app\common\model\User();

        $result = $model->where(['phone'=>$phone])->find();


        if (!$result){
            return ['code'=>0,'msg'=>'账号不存在'];
        }

        $realPwd = (new \app\common\tool\User())->makePassword($result['salt'],$pwd);

        if ($realPwd != $result['password']) return ['code'=>0,'msg'=>'密码不正确'];

        return ['code'=>1,'msg'=>'success','token'=>$result['token']];

    }

    public function existsPhone($phone)
    {
        $result = (new UserModel())->where(['phone'=>$phone])->find();

        return $result ? $result : false;
    }
}