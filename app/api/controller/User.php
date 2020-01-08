<?php
declare (strict_types = 1);

namespace app\api\controller;

use app\common\service\Category;
use app\common\tool\Sms;
use app\common\typeCode\cate\CUserLicense;
use app\common\typeCode\cate\CUserLicenseProperty;
use think\Request;
use think\Validate;
use app\common\service\User as Service;

class User extends Base
{
    public function register(Request $request)
    {
        $post = $request->post();

        $rules = [
            'phone|手机号'        => 'require|mobile|max:11',
            'pwd|密码'       => 'require|max:32',
            're_pwd|确认密码'        => 'require|max:32',
            'code|验证码'          => 'require',
        ];

        $validate = new Validate();

        $validate->rule($rules);

        if (!$validate->check($post)){
            return json(['code'=>0,'msg'=>$validate->getError()]);
        }
        //检查两次密码是否一致
        if ($post['pwd'] != $post['re_pwd']){
            return json(['code'=>0,'msg'=>'两次密码不一致']);
        }

        //检查是否有手机号重复的
        if ((new Service())->existsPhone($post['phone'])){
            return json(['code'=>0,'msg'=>'手机号已被注册']);
        }

        //检查验证码是否正确
        $Sms = (new Sms('SMS_CODE','user',300));
        if (!$Sms->checkPhoneCode($post['phone'],$post['code'])){
            return json(['code'=>0,'msg'=>'验证码不正确']);
        }

        //入库
        $data = [
            'phone' => $post['phone'],
            'password' => $post['pwd'],
        ];

        $id = (new Service())->insert($data);

        return json(['code'=>1,'msg'=>'success']);
    }

    public function getPhoneCode(Request $request)
    {
        $phone = $request->get('phone');

        if (!$phone) return json(['code'=>0,'msg'=>'validErr']);

        $Sms = (new Sms('SMS_CODE','user',300));

        $result = $Sms->sendCodeForPhone($phone);

        if ($result['code'] != 1) return json($result);

        return json(['code'=>1,'msg'=>'success']);

    }

    public function login(Request $request)
    {
        $post = $request->post();

        $rules = [
            'phone|手机号'        => 'require|mobile|max:11',
            'pwd|密码'            => 'require|max:32',
        ];

        $validate = new Validate();

        $service = new Service();

        $validate->rule($rules);

        if (!$validate->check($post)){
            return json(['code'=>0,'msg'=>$validate->getError()]);
        }

        $result = $service->login($post['phone'],$post['pwd']);

        if ($result['code'] == 0) return json($result);

        return json(['code'=>1,'msg'=>'success','data'=>$result['token']]);
    }

    public function setAuth(Request $request)
    {
        $user = $this->userInfo;

        if ($user['license_status'] == 3) return json(['code'=>0,'msg'=>'您已通过审核,无需再次认证']);

        if ($user['license_status'] == 2) return json(['code'=>0,'msg'=>'您已提交过认证信息,请耐心等待审核']);

        $post = $request->post();
        /**
         */

        //基本信息认证
        $rules = [
            'name|姓名'                      => 'require|max:30',
            'sex|性别'                       => 'require',
            'work_email|工作邮箱'            => 'require|email',
            'ent_name|公司全称'              => 'require|max:128',
//            'ent_bus_area_ids|投放区域'      => 'require',  //暂时不用
            'ent_bus_province|投放区域所在省'      => 'require',
            'ent_bus_city|投放区域所在市'      => 'require',
            'ent_bus_county|投放区域所在县'      => 'require',
            'ent_type|品牌类型'               => 'require',
            'ent_province|公司所在省'             => 'require',
            'ent_city|公司所在市'                 => 'require',
            'ent_county|公司所在区'               => 'require',
            'ent_address|详细地址'           => 'require|max:128',
            'license_name|证件姓名'     => 'require|max:30',
//            'license_type|证件类型'     => 'require',
            'license_number|证件号'     => 'require|max:60',
            'license_pic_of_top|身份证正面' => 'require|max:128',
            'license_pic_of_under|身份证背面' => 'require|max:128',
            'ent_license_name|公司名称'             => 'require|max:30',
            'ent_license_property_type|公司性质'    => 'require',
            'ent_license_bus_license|营业执照'      => 'require|max:512',
        ];

        $validate = new Validate();

        $validate->rule($rules);

        if (!$validate->check($post)){
            return json(['code'=>0,'msg'=>$validate->getError()]);
        }

        $result = (new \app\common\service\User())->auth($user['id'],$post);

        if(!$result) return json(['code'=>0,'msg'=>'提交失败']);

        return json(['code'=>1,'msg'=>'success']);
    }

    public function loginWithCode()
    {

    }

    public function getLicenseCate()
    {
        return json(['code'=>1,'msg'=>'success','data'=>(new Category())->getList((new CUserLicense()))]);
    }

    public function getLicensePropertyCate()
    {
        return json(['code'=>1,'msg'=>'success','data'=>(new Category())->getList((new CUserLicenseProperty()))]);
    }

    /**
     * 修改用户的基础信息  昵称 头像
     * $data 8/1/2020 下午2:10
     */
    public function basics(Request $request)
    {
        $user = $this->userInfo;

        $data = $request->put();

        $rules = [
            'nickname|昵称'        => 'require|max:8',
        ];

        $validate = new Validate();

        $validate->rule($rules);

        if (!$validate->check($data)){
            return json(['code'=>0,'msg'=>$validate->getError()]);
        }

        $result = (new Service)->basics($user['id'],$data);

        if(!$result) return json(['code'=>0,'保存失败']);

        return json(['code'=>1,'msg'=>'success']);
    }
}
