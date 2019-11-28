<?php
declare (strict_types = 1);

namespace app\bAdmin\controller;

use app\BaseController;
use app\common\service\Category as CateService;
use app\common\tool\Upload;
use app\common\typeCode\cate\ABus as TypeDesc;
use think\Validate;
use think\facade\View;
use think\Request;

class AUser extends BaseController
{
    public function index()
    {
        $list = (new \app\common\service\AUser())->getAList(true);

        View::assign('list',$list);

        return view();

    }

    public function add()
    {
        //获取a端行业分类
        $busCate = (new CateService())->getList((new TypeDesc()));

        View::assign('bus_cate',$busCate);

        return view();
    }

    public function save(Request $request)
    {
        $post = $request->post();

        try{
            $validate = new Validate();

            $rules = [
                'address|公司详细地址'=>'require|max:128',
                'bus_license|营业执照'=>'require|max:1000',
                'bus_license_code|营业执照代码'=>'require|max:100',
                'province|地址'=>'require',
                'city|地址'=>'require',
                'county|地址'=>'require',
                'contact|联系人姓名'=>'require|max:30',
                'contact_license_code|联系人身份证号'=>'require|max:18',
                'contact_license_pic|联系人身份证照片'=>'require|max:500',
                'contact_sex|联系人性别'=>'require',
                'contact_tel|联系人电话'=>'require|max:20',
                'contact_wechat|联系人微信'=>'require|max:127',
                'credit_code|统一社会信用代码'=>'require|max:18',
                'email|工作邮箱'=>'require|email',
                'name|企业名称'=>'require|max:31',
                'pro_id|行业分类'=>'require',
                'type|账号类型'=>'require',
//                '__token__'     => 'token',
            ];
            $validate->rule($rules);

            if(!$validate->check($post))  throw new \Exception($validate->getError());
        }catch (\Exception $e){

        }


    }

    public function uploadPic()
    {
        return json((new Upload())->uploadOnePic('aUser/'));
    }
}
