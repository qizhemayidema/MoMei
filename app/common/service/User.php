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

    public function auth($userId,$data)
    {
        $auth = [
            'name'                 => $data['name'],
            'sex'                  => $data['sex'],
            'work_email'           => $data['work_email'],
            'ent_name'             => $data['ent_name'],
//            'ent_bus_area_ids'     => $data['ent_bus_area_ids'],  //暂时不用
            'ent_bus_province'     => $data['ent_bus_province'],
            'ent_bus_city'     => $data['ent_bus_city'],
            'ent_bus_county'     => $data['ent_bus_county'],
            'ent_type'             => $data['ent_type'],
//            'ent_province_id'      => $data['ent_province_id'],   //没有存省市县的id   只存了省市县的名称
//            'ent_city_id'          => $data['ent_city_id'],
//            'ent_county_id'        => $data['ent_county_id'],
            'ent_address'          => $data['ent_address'],
            'license_name'     => $data['license_name'],
//            'license_type'     => $data['license_type'],  //只有居民身份证类型  所以这个不用了
            'license_number'   => $data['license_number'],
            'license_pic_of_top' => $data['license_pic_of_top'],
            'license_pic_of_under' => $data['license_pic_of_under'],
            'ent_license_name'     => $data['ent_license_name'],
            'ent_license_property_type'  => $data['ent_license_property_type'],
            'ent_license_bus_license' => $data['ent_license_bus_license'],
        ];

        $areaService = new Area();
        $cateService = new Category();

        $auth['ent_province'] = $data['ent_province'];
        $auth['ent_city'] = $data['ent_city'];
        $auth['ent_county'] = $data['ent_county'];
//        $auth['license_type_str'] = $cateService->get($data['license_type'])['name'];
        $auth['license_type_str'] = '居民身份证';   //这里用户只有居民身份证证件
        $auth['ent_license_property_type_str'] = $cateService->get($data['ent_license_property_type'])['name'];

        $auth['license_status'] = 2;

        return (new UserModel())->where(['id'=>$userId])->update($auth);

    }

    public function changeAuthStatus($userId,$status)
    {
        (new UserModel())->where(['id'=>$userId])->update(['license_status'=>$status]);
    }

    public function existsPhone($phone)
    {
        $result = (new UserModel())->where(['phone'=>$phone])->find();

        return $result ? $result : false;
    }

    /**
     * 修改用户的基础信息  头像 昵称
     * @param $userId
     * @param $data
     * @return UserModel
     * $data 8/1/2020 下午2:57
     */
    public function basics($userId,$data)
    {
        $upData = [];
        (isset($data['nickname'])) ? $upData['nickname'] = $data['nickname'] :'';
        (isset($data['head_portrait'])) ? $upData['head_portrait'] = $data['head_portrait'] :'';
        return (new UserModel())->where(['id'=>$userId])->update($upData);
    }
}