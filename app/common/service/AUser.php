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

    //排序
    private $order = [];

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

    public function order($field,$order)
    {
        $this->order[0] = $field;
        $this->order[1] = $order;

        return $this;
    }


    public function getList()
    {
        $handler = new AUserModel();

        $handler = $this->showType ? $handler->backgroundShowData() : $handler->receptionShowData();

        $handler = $this->aUserImpl ? $handler->where(['type'=>$this->aUserImpl->getUserType()]) : $handler;

        $handler = $this->order ? $handler->order($this->order[0],$this->order[1]) : $handler;

        return $this->pageLength ? $handler->paginate($this->pageLength) : $handler->select();
    }

    public function existsUsername($username,$type = null,$exceptId = 0)
    {
        if (!$type) $type = $this->aUserImpl->getUserType();

        $handler = (new AUserModel());

        if ($exceptId) $handler = $handler->where('id','<>',$exceptId);

        $result = $handler->where(['type'=>$type,'username'=> $username])->find();

        return  $result ? $result : null;
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
            'box_office_for_year'  => $data['box_office_for_year'] ?? '',
            'watch_mv_sum'  => $data['watch_mv_sum'] ?? '',
            'ticket_price_for_average'  => $data['ticket_price_for_average'] ?? '',
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


    public function existsUsernameReturnInfo($username,$type = null,$exceptId = 0)
    {
        $handler = (new AUserModel());

        if (!$type) $type = $this->aUserImpl->getUserType();

        if ($exceptId) $handler = $handler->where('id','<>',$exceptId);

        return $handler->where(['username'=> $username])->find();
    }

    public function verifyAccount($username,$password,$slat,$type = null)
    {
        if (!$type) $type = $this->aUserImpl->getUserType();

        $passwordSlat = md5($password.$slat);

        return (new AUserModel())->where(['username'=>$username,'password'=>$passwordSlat])->find() ? true : false;
    }

    /**
     * 根据影投的类型和id查询关联的影院
     * @param $type     int    资源方是影投 还是院线
     * @param $id       int    资源方的id
     * $data 2019/12/2 14:18
     */
    public function getAssociatedCinemaList($type,$id,$page=false)
    {
        $handler = new \app\common\model\Cinema();
        if($type==1){
            $handler = $handler->where('tou_id',$id);
        }elseif ($type==2){
            $handler = $handler->where('yuan_id',$id);
        }
        return $page ? $handler->paginate($page) : $handler->select()->toArray();
    }
}