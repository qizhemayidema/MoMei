<?php
declare (strict_types = 1);

namespace app\bAdmin\controller;

use app\common\service\Area;
use app\common\service\Category;
use app\common\tool\Upload;
use app\common\typeCode\aUser\Ying;
use app\common\typeCode\aUser\Yuan;
use app\common\typeCode\cate\CBus;
use app\common\typeCode\cate\CinemaNearby;
use think\exception\ValidateException;
use app\common\service\Cinema as Service;
use think\Request;
use think\facade\View;
use think\Validate;

class Cinema extends Base
{
    public function index()
    {
        $list = (new Service())->getList(true,10);

        View::assign('list',$list);

        return view();
    }

    public function add()
    {
        $cateService = new Category();
        //查询行业分类
        $cate = $cateService->getList((new CBus()));

        //从获取城市列表
        $area = (new Area())->getListByPId();

        //查询院线列表
        $yuan = (new \app\common\service\AUser((new Yuan())))->getList();

        //查询影投列表
        $ying = (new \app\common\service\AUser((new Ying())))->showType(true)->getList();

        //获取周边分类列表
        $zhou = $cateService->getList((new CinemaNearby()));

        View::assign('area',$area);
        View::assign('yuan',$yuan);
        View::assign('ying',$ying);
        View::assign('bus_cate',$cate);
        View::assign('zhou',$zhou);
        return view();
    }

    public function save(Request $request)
    {
//        return json($request->post());
        $post = $request->post();

        $validate = new Validate();

        $rules = [
            'pro_id'          => 'require',
            'yuan_id'         => 'require',
            'tou_id'          => 'require',
//            'area_id'
            'username|账户名'  => 'require|max:32',
            'password|密码'    => 'require',
            're_password|确认密码' => 'require|confirm:password',
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

        $model = new \app\common\model\Cinema();
        try{
            if (!$post['yuan_id'] && !$post['tou_id']) throw new ValidateException('影投和院线必须至少选一个');

            $model->startTrans();

            if(!$validate->check($post))  throw new ValidateException($validate->getError());

            $service = new Service();

            if($service->existsUsername($post['username']))
            {
                throw new ValidateException('该用户名已存在');
            }

            //查询行业名称
            $post['pro_name'] = (new \app\common\model\Category())->get($post['pro_id'])['name'];

            //处理表变组合
            if (!$post['area_id']){
                $post['area_id'] = 0;
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

            $service->insert($post);

            $model->commit();

            return json(['code'=>1,'msg'=>'success']);
        }catch (ValidateException $e){

            $model->rollback();
            return json(['code'=>0,'msg'=>$e->getMessage()]);

        }catch (\Exception $e){

            $model->rollback();
            return json(['code'=>0,'msg'=>$e->getMessage()]);

        }

        return json(['code'=>1,'msg'=>'success']);
    }

    public function edit(Request $request)
    {
        $id = $request->param('id');

        $cateService = new Category();

        $service = new Service();

        $area = new Area();
        //获取数据
        $data = $service->get($id);

        //查询行业分类
        $cate = $cateService->getList((new CBus()));

        //获取城市一级列表
        $area1 = $area->getListByPId();

        //获取城市二级列表
        $area2 = $area->getListByPId($data['province_id']);

        //获取城市三级列表
        $area3 = $area->getListByPId($data['city_id']);

        //查询院线列表
        $yuan = (new \app\common\service\AUser((new Yuan())))->getList();

        //查询影投列表
        $ying = (new \app\common\service\AUser((new Ying())))->showType(true)->getList();

        //获取周边分类列表
        $zhou = $cateService->getList((new CinemaNearby()));

        View::assign('area1',$area1);
        View::assign('area2',$area2);
        View::assign('area3',$area3);
        View::assign('yuan',$yuan);
        View::assign('ying',$ying);
        View::assign('bus_cate',$cate);
        View::assign('zhou',$zhou);
        View::assign('data',$data);

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
            'yuan_id'         => 'require',
            'tou_id'          => 'require',
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

        $model = new \app\common\model\Cinema();
        try{
            if (!$post['yuan_id'] && !$post['tou_id']) throw new ValidateException('影投和院线必须至少选一个');

            $model->startTrans();

            if(!$validate->check($post))  throw new ValidateException($validate->getError());

            $service = new Service();

            if($service->existsUsername($post['username'],$post['id']))
            {
                throw new ValidateException('该用户名已存在');
            }

            //查询行业名称
            $post['pro_name'] = (new \app\common\model\Category())->get($post['pro_id'])['name'];

            //处理表变组合
            if (!$post['area_id']){
                $post['area_id'] = 0;
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

//            return json(['code'=>0]);
            $service->update($post['id'],$post);

            $model->commit();

        }catch (ValidateException $e){

            $model->rollback();
            return json(['code'=>0,'msg'=>$e->getMessage()]);

        }catch (\Exception $e){

            $model->rollback();
            return json(['code'=>0,'msg'=>$e->getMessage()]);

        }

        return json(['code'=>1,'msg'=>'success']);
    }

    public function changeStatus(Request $request)
    {
        $status = $request->post('status');
        $id = $request->post('id');

        (new Service())->changeStatus($id,$status);

        return json(['code'=>1,'msg'=>'success']);
    }

    public function info(Request $request)
    {
        $id = $request->param('id');

        $info = (new Service())->get($id);

        $aUserModel = new \app\common\service\AUser();

        $tou = $aUserModel->get($info['tou_id']);

        $yuan =  $aUserModel->get($info['yuan_id']);

        View::assign(compact('info','tou','yuan'));

        return view();

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
