<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/12/4
 * Time: 14:52
 */

namespace app\cinemaAdmin\controller;


use app\common\service\Category;
use app\common\service\Manager as Service;
use app\common\service\Area;
use app\common\typeCode\cate\ABus;
use app\common\typeCode\cate\CinemaNearby;
use app\common\service\CategoryObjHaveAttr;
use app\Request;
use app\common\tool\Session;
use think\facade\View;
use think\Validate;
use think\exception\ValidateException;
use app\common\tool\Upload;

class BasicInformation extends Base
{
    public function edit(Request $request)
    {
        $info = (new Session())->getData();

        $id = $info['id'];

        $cateService = new Category();

        $service = new Service((new \app\common\typeCode\manager\Cinema()));

        $area = new Area();
        //获取数据
        $user = $service->get($id);

        $data = $service->getInfo($user['info_id']);

        //查询行业分类
        $cate = $cateService->getList((new ABus()));

        //获取城市一级列表
        $area1 = $area->getListByPId();

        //获取城市二级列表
        $area2 = $area->getListByPId($data['province_id']);

        //获取城市三级列表
        $area3 = $area->getListByPId($data['city_id']);


        //获取周边分类列表
        $zhou = $cateService->getList((new CinemaNearby()));

        //获取级别分类列表
        $level = $cateService->getList((new \app\common\typeCode\cate\CinemaLevel()));

        foreach ($level as $key => $value){
            $level[$key]['attr'] =  $cateService->getAttrList($value['id']);
        }

        //获取数据级别选中状态
        $levelCheck = (new CategoryObjHaveAttr(1))->getIdColumns($user['group_code']);

        View::assign('area1',$area1);
        View::assign('area2',$area2);
        View::assign('area3',$area3);
        View::assign('bus_cate',$cate);
        View::assign('zhou',$zhou);
        View::assign('user',$user);
        View::assign('data',$data);
        View::assign('level',$level);
        View::assign('level_check',$levelCheck);

        return view();
    }

    public function update(Request $request)
    {
//        return json($request->post());
        $post = $request->post();

        $validate = new Validate();

        $rules = [
            'id'              => 'require',
            'pro_id'          => 'require',
//            'yuan_id'         => 'require',
//            'tou_id'          => 'require',
//            'area_id'
            'username|账户名'  => 'require|max:32',
//            'password|密码'    => '',
            're_password|确认密码' => 'confirm:password',
            'address|公司详细地址'=>'require|max:128',
            'bus_license|营业执照'=>'require|max:1000',
            'bus_license_code|营业执照代码'=>'require|max:100',
            'province|地址'=>'require',
            'city|地址'=>'require',
            'county|地址'=>'require',
            'tel|公司电话' => 'require|max:20',
            'property_company|物业公司电话' => 'require|max:50',
            'contact|联系人姓名'=>'require|max:30',
            'contact_license_code|联系人身份证号'=>'require|max:18',
            'contact_license_pic|联系人身份证照片'=>'require|max:500',
            'contact_sex|联系人性别'=>'require',
            'contact_tel|联系人电话'=>'require|max:20',
            'contact_wechat|联系人微信'=>'require|max:127',
            'credit_code|统一社会信用代码'=>'require|max:18',
            'email|工作邮箱'=>'require|email',
            'name|影院名称'=>'require|max:31',
            'bus_area|营业面积' => 'require|integer',
            'duty|负责人'      => 'require|max:30',
            'duty_tel|负责人电话'  => 'require|max:30',

            'pro_id|行业分类'=>'require',
//                '__token__'     => 'token',
        ];

        $validate->rule($rules);

        $model = new \app\common\model\Manager();
        try{
//            if (!$post['yuan_id'] && !$post['tou_id']) throw new ValidateException('影投和院线必须至少选一个');

            $model->startTrans();

            if(!$validate->check($post))  throw new ValidateException($validate->getError());

            $service = new Service((new \app\common\typeCode\manager\Cinema()));

            if($service->existsUsername($post['username'],$post['id']))
            {
                throw new ValidateException('账户已存在');
            }

            //查询行业名称
            $post['pro_name'] = (new \app\common\model\Category())->get($post['pro_id'])['name'];

            //处理表变组合
            if (!$post['area_id']){
                $post['area_id'] = 0;
                $post['area_value'] = '';
            }else{
                $area = explode('-',$post['area_id']);
                $post['area_id'] = $area[0];
                $post['area_value'] = $area[1];
            }

            $province = explode('-',$post['province']);
            $city = explode('-',$post['city']);
            $county = explode('-',$post['county']);

            $post['city_id'] = $city[0];
            $post['city'] = $city[1];
            $post['province_id'] = $province[0];
            $post['province'] = $province[1];
            $post['county_id'] = $county[0] ?? 0;
            $post['county'] = $county[1] ?? '';

            $oldUser = $service->get($post['id']);

            $post['role_id'] = $oldUser['role_id'];
            $post['role_name'] = $oldUser['role_name'];

            $service->update($post['id'],$post);

            $service->updateInfo($oldUser['info_id'],$post);

            $model->commit();

        }catch (ValidateException $e){

            $model->rollback();
            return json(['code'=>0,'msg'=>$e->getMessage()]);

        }catch (\Exception $e){

            $model->rollback();
            return json(['code'=>0,'msg'=>$e->getMessage().$e->getLine().$e->getFile()]);

        }

        return json(['code'=>1,'msg'=>'success']);
    }

    public function getArea(Request $request)
    {
        $id = $request->post('p_id');

        //从获取城市列表
        $area = (new Area())->getListByPId($id);

        return json(['code'=>1,'data'=>$area]);
    }

    public function uploadPic()
    {
        return json((new Upload())->uploadOnePic('cinemaUser/'));
    }
}