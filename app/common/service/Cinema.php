<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/2
 * Time: 11:50
 */

namespace app\common\service;


class Cinema
{
    public function getList($isAll = false,$page = null)
    {
        $handler = new \app\common\model\Cinema();

        $handler = $isAll ? $handler->backgroundShowData() : $handler->receptionShowData();

        $result = $page ?  $handler->paginate($page) : $handler->select()->toArray();

        return $result;
    }

    public function get($id)
    {
        return (new \app\common\model\Cinema())->find($id);
    }

    public function insert($data)
    {
        /**
        'area_id'
        'area_value
         */

        $model = new \app\common\model\Cinema();

        //创建盐值
        $salt = md5(mt_rand(10000000000,99999999999));

        $insert = [
            'yuan_id'   => $data['yuan_id'],
            'tou_id'    => $data['tou_id'],
            'area_id'   => $data['area_id'] ?? 0,
            'area_value' => $post['area_value'] ?? '',
            'bus_area'  => $data['bus_area'],
            'property_company' => $data['property_company'],
            'username'  => $data['username'],
            'slat'      => $salt,
            'password'  => md5($data['password'] . $salt),
            'address'  => $data['address'],
            'bus_license'  => $data['bus_license'],
            'bus_license_code'  => $data['bus_license_code'],
            'province_id'  => $data['province_id'],
            'province'  => $data['province'],
            'city_id'  => $data['city_id'],
            'city'  => $data['city'],
            'county_id'  => $data['county_id'],
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
            'role_id'  => $data['role_id'] ?? 0,
            'pro_name' => $data['pro_name'],
            'duty'      => $data['duty'],
            'duty_tel'  => $data['duty_tel'],
            'create_time' => time(),
        ];

        isset($data['group_code']) && $insert['group_code'] = $data['group_code'];

        $model->insert($insert);

        $id = $model->getLastInsID();

        if (!isset($data['group_code'])){
            $data['group_code'] = $id;
            $model->where(['id'=>$id])->update(['group_code'=>$id]);
        }
//        $userName = mb_substr(uniqid(true) . time() . md5(mt_rand(10000000000,99999999999) . $salt),0,23);

        return $data;

    }

    public function update($id,$data)
    {

        $model = new \app\common\model\Cinema();

        $update = [
            'yuan_id'   => $data['yuan_id'],
            'tou_id'    => $data['tou_id'],
            'area_id'   => $data['area_id'] ?? 0,
            'area_value' => $post['area_value'] ?? '',
            'bus_area'  => $data['bus_area'],
            'property_company' => $data['property_company'],
            'username'  => $data['username'],
//            'slat'      => $salt,
//            'password'  => md5($data['password'] . $salt),
            'address'  => $data['address'],
            'bus_license'  => $data['bus_license'],
            'bus_license_code'  => $data['bus_license_code'],
            'province_id'  => $data['province_id'],
            'province'  => $data['province'],
            'city_id'  => $data['city_id'],
            'city'  => $data['city'],
            'county_id'  => $data['county_id'],
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
            'role_id'  => $data['role_id'] ?? 0,
            'pro_name' => $data['pro_name'],
            'duty'      => $data['duty'],
            'duty_tel'  => $data['duty_tel'],
            'create_time' => time(),
        ];


        if (isset($data['password']) && $data['password']){
            $update['slat'] =  md5(mt_rand(10000000000,99999999999));
            $update['password'] =  md5($data['password'] . $update['slat']);
        }


        $model->modify($id,$update);
    }

    public function existsUsername($username,$exceptId = 0)
    {
        $handler = (new \app\common\model\Cinema());

        if ($exceptId) $handler = $handler->where('id','<>',$exceptId);

        return $handler->where(['username'=> $username])->find() ? true : false;
    }


    public function changeStatus($id,$status)
    {
        (new \app\common\model\Cinema())->modify($id,['status'=>$status]);
    }

    /**
     * 查询某资源方下的影院总数 厅总数 座位总数
     * @param $id           int   资源方id
     * @param $type         int   类型  1 影投 2 院线
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * $data 2019/12/3 13:15
     */
    public function getCinemaAmountCount($id,$type)
    {
        $cinemaModel = new  \app\common\model\Cinema();
        if($type==1){
            $cinemaModel = $cinemaModel->belongToTou($id);
        }elseif ($type==2){
            $cinemaModel = $cinemaModel->belongToYuan($id);
        }
        return $cinemaModel->field('count(*) as cinemaCount,sum(screen_sum) as screenSum,sum(seat_sum) as seatSum')->select()->toArray();
    }
}