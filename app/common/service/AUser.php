<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/26
 * Time: 15:48
 */

namespace app\common\service;


use app\common\typeCode\AUserImpl;
use app\common\model\AUser as AUserModel;
use app\Request;

class AUser
{
    //查询分页长度 如果 null则全取
    private $pageLength = null;

    //查询展示类型 如果false 被冻结的一些数据都不展示
    private $showType = false;

    //描述类
    private $aUserImpl = null;

    public function __construct(?AUserImpl $aUserImpl = null)
    {
        $this->aUserImpl = $aUserImpl;
    }

    public function showType($showType = false)
    {
        $this->showType = $showType;

        return $this;
    }

    public function pageLength($pageLength = 15)
    {
        $this->pageLength = $pageLength;

        return $this;
    }


    public function getList()
    {
        $handler = new AUserModel();

        $handler = $this->showType ? $handler->backgroundShowData() : $handler->receptionShowData();

        $handler = $this->aUserImpl ? $handler->where(['type'=>$this->aUserImpl->getUserType()]) : $handler;

        return $this->pageLength ? $handler->paginate($this->pageLength) : $handler->select();
    }

    public function existsUsername($username,$type = null,$exceptId = 0)
    {
        if (!$type) $type = $this->aUserImpl->getUserType();

        $handler = (new AUserModel());

        if ($exceptId) $handler = $handler->where('id','<>',$exceptId);

        return $handler->where(['type'=>$type,'username'=> $username])->find() ? true : false;
    }

    public function insert($data)
    {
        $aUserModel = (new AUserModel());

        //查询行业名称
        $proName = (new \app\common\model\Category())->get($data['pro_id'])['name'];

        //创建盐值
        $salt = md5(mt_rand(10000000000,99999999999));

        $insert = [
            'username'  => $data['username'],
            'slat'      => $salt,
            'password'  => md5($data['password'] . $salt),
            'address'  => $data['address'],
            'bus_license'  => $data['bus_license'],
            'bus_license_code'  => $data['bus_license_code'],
            'province'  => $data['province'],
            'city'  => $data['city'],
            'county'  => $data['county'],
            'contact'  => $data['contact'],
            'tel'       => $data['tel'],
            'contact_license_code'  => $data['contact_license_code'],
            'contact_license_pic'  => $data['contact_license_pic'],
            'contact_sex'  => $data['contact_sex'],
            'contact_tel'  => $data['contact_tel'],
            'contact_wechat'  => $data['contact_wechat'],
            'credit_code'  => $data['credit_code'],
            'email'  => $data['email'],
            'name'  => $data['name'],
            'pro_id'  => $data['pro_id'],
            'type'  => $data['type'],
            'role_id'  => $data['role_id'] ?? 0,
            'pro_name' => $proName,
            'create_time' => time(),
        ];

        isset($data['group_code']) && $insert['group_code'] = $data['group_code'];

        $aUserModel->insert($insert);

        $id = $aUserModel->getLastInsID();

        if (!isset($data['group_code'])){
            $data['group_code'] = $id;
            $aUserModel->where(['id'=>$id])->update(['group_code'=>$id]);
        }
//        $userName = mb_substr(uniqid(true) . time() . md5(mt_rand(10000000000,99999999999) . $salt),0,23);

        return $data;

    }

    public function update($id,$data)
    {
        $aUserModel = (new AUserModel());

        //查询行业名称
        $proName = (new \app\common\model\Category())->get($data['pro_id'])['name'];


        $update = [
            'username'  => $data['username'],
//            'slat'      => $salt,
//            'password'  => md5($data['password'] . $salt),
            'address'  => $data['address'],
            'bus_license'  => $data['bus_license'],
            'bus_license_code'  => $data['bus_license_code'],
            'province'  => $data['province'],
            'city'  => $data['city'],
            'county'  => $data['county'],
            'contact'  => $data['contact'],
            'tel'       => $data['tel'],
            'contact_license_code'  => $data['contact_license_code'],
            'contact_license_pic'  => $data['contact_license_pic'],
            'contact_sex'  => $data['contact_sex'],
            'contact_tel'  => $data['contact_tel'],
            'contact_wechat'  => $data['contact_wechat'],
            'credit_code'  => $data['credit_code'],
            'email'  => $data['email'],
            'name'  => $data['name'],
            'pro_id'  => $data['pro_id'],
            'type'  => $data['type'],
            'role_id'  => $data['role_id'] ?? 0,
            'pro_name' => $proName,
        ];

        if (isset($data['password']) && $data['password']){
            $update['slat'] =  md5(mt_rand(10000000000,99999999999));
            $update['password'] =  md5($data['password'] . $update['slat']);
        }


        $aUserModel->modify($id,$update);
    }

    public function changeStatus($id,$status)
    {
        (new AUserModel())->modify($id,['status'=>$status]);
    }

    public function get($id)
    {
        return (new AUserModel())->get($id);
    }

    public function delete($id)
    {
        (new AUserModel())->softDelete($id);
    }
}