<?php
declare (strict_types = 1);

namespace app\bAdmin\controller;

use app\common\service\Area;
use app\common\service\Category;
use app\common\service\CategoryObjHave;
use app\common\service\CategoryObjHaveAttr;
use app\common\tool\Upload;
use app\common\typeCode\manager\Ying;
use app\common\typeCode\manager\Yuan;
use app\common\typeCode\cate\ABus;
use app\common\typeCode\cate\CinemaNearby;
use think\exception\ValidateException;
use app\common\service\Manager as Service;
use think\Request;
use think\facade\View;
use think\Validate;

class Cinema extends Base
{
    public function index()
    {
        $list = (new Service((new \app\common\typeCode\manager\Cinema())))->showType(true)->pageLength(15)->getList();

        View::assign('list',$list);

        return view();
    }

    public function add()
    {
        $cateService = new Category();

        //从获取城市列表
        $area = (new Area())->getListByPId();

        //查询院线列表
        $yuan = (new Service((new Yuan())))->showType(true)->getInfoList();

        //查询影投列表
        $ying = (new Service((new Ying())))->showType(true)->getInfoList();

        //获取周边分类列表
        $zhou = $cateService->getList((new CinemaNearby()));

        //获取级别分类列表
        $level = $cateService->getList((new \app\common\typeCode\cate\CinemaLevel()));

        foreach ($level as $key => $value){
            $level[$key]['attr'] =  $cateService->getAttrList($value['id']);
        }

//        dump($level);die;
        View::assign('area',$area);
        View::assign('yuan',$yuan);
        View::assign('ying',$ying);
        View::assign('zhou',$zhou);
        View::assign('level',$level);

        return view();
    }

    public function save(Request $request)
    {
//        return json($request->post());
        $post = $request->post();

        $validate = new Validate();

        $rules = [
            'yuan_id'         => 'require',
            'tou_id'          => 'require',
//            'area_id'
            'username|账户名'  => 'require|max:32',
            'password|密码'    => 'require',
            're_password|确认密码' => 'require|confirm:password',
            'address|公司详细地址'=>'require|max:128',
            'bus_license|营业执照'=>'require|max:1000',
            'province|地址'=>'require',
            'city|地址'=>'require',
            'county|地址'=>'require',
            'tel|公司电话' => 'require|max:20',
            'property_company|物业公司名称' => 'require|max:50',
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

//                '__token__'     => 'token',
        ];

        $validate->rule($rules);

        $model = new \app\common\model\Manager();
        try{
            if (!$post['yuan_id'] && !$post['tou_id']) throw new ValidateException('影投和院线必须至少选一个');

            $model->startTrans();

            if(!$validate->check($post))  throw new ValidateException($validate->getError());

            $service = new Service((new \app\common\typeCode\manager\Cinema()));

            if($service->existsUsername($post['username']))
            {
                throw new ValidateException('账户已存在');
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

            $data = $service->insert($post);

//            return json(['code'=>0]);
            $id = $data['id'];

            if (isset($data['level_name'])){
                //新增影院相关级别
                $levels = $data['level_name'];
                $options = $data['level_option'];

                (new CategoryObjHaveAttr(1))->insert($id,$levels,$options);

            }

            if (isset($post['area_around'])){

                $cateObjHaveService = new CategoryObjHave((new \app\common\typeCode\cateObjHave\Cinema()));

                $areaAroundArr = [];

                foreach ($post['area_around'] as $k => $v){

                    $aroundTemp = explode('-',$v);

                    $areaAroundArr[] = [
                        'cate_id'   => $aroundTemp[0],
                        'cate_name' => $aroundTemp[1]
                    ];
                }

                $cateObjHaveService->insertAll($areaAroundArr,(new CinemaNearby()),$data['group_code']);
            }

            $model->commit();

            return json(['code'=>1,'msg'=>'success']);
        }catch (ValidateException $e){

            $model->rollback();
            return json(['code'=>0,'msg'=>$e->getMessage()]);

        }catch (\Exception $e){

            $model->rollback();
            return json(['code'=>0,'msg'=>$e->getMessage().$e->getLine()]);

        }

        return json(['code'=>1,'msg'=>'success']);
    }

    public function edit(Request $request)
    {
        $id = $request->param('id');

        $cateService = new Category();

        $service = new Service((new \app\common\typeCode\manager\Cinema()));

        $area = new Area();

        $cinemaEarByTypeCode = (new CinemaNearby());

        //获取数据
        $user = $service->get($id);

        $data = $service->getInfo($user['info_id']);

        //获取城市一级列表
        $area1 = $area->getListByPId();

        //获取城市二级列表
        $area2 = $area->getListByPId($data['province_id']);

        //获取城市三级列表
        $area3 = $area->getListByPId($data['city_id']);

        //查询院线列表
        $yuan = (new Service((new Yuan())))->showType(true)->getInfoList();

        //查询影投列表
        $ying = (new Service((new Ying())))->showType(true)->getInfoList();

        //获取周边分类列表
        $zhou = $cateService->getList($cinemaEarByTypeCode);

        //获取级别分类列表
        $level = $cateService->getList((new \app\common\typeCode\cate\CinemaLevel()));

        //获取影院的周边选择
        $selectAroundList = (new CategoryObjHave((new \app\common\typeCode\cateObjHave\Cinema())))->getList($cinemaEarByTypeCode,$user['group_code']);

        $selectAroundIds = [];
        foreach ($selectAroundList as $k => $v){
            $selectAroundIds[] = $v['cate_id'];
        }


        foreach ($level as $key => $value){
            $level[$key]['attr'] =  $cateService->getAttrList($value['id']);
        }

        //获取数据级别选中状态
        $levelCheck = (new CategoryObjHaveAttr(1))->getIdColumns($user['group_code']);

        View::assign('area1',$area1);
        View::assign('area2',$area2);
        View::assign('area3',$area3);
        View::assign('yuan',$yuan);
        View::assign('ying',$ying);
        View::assign('zhou',$zhou);
        View::assign('user',$user);
        View::assign('data',$data);
        View::assign('level',$level);
        View::assign('level_check',$levelCheck);
        View::assign('select_around_ids',$selectAroundIds);

        return view();
    }

    public function update(Request $request)
    {
//        return json($request->post());
        $post = $request->post();

        $validate = new Validate();

        $rules = [
            'id'              => 'require',
            'yuan_id'         => 'require',
            'tou_id'          => 'require',
//            'area_id'
            'username|账户名'  => 'require|max:32',
//            'password|密码'    => '',
            're_password|确认密码' => 'confirm:password',
            'address|公司详细地址'=>'require|max:128',
            'bus_license|营业执照'=>'require|max:1000',
            'province|地址'=>'require',
            'city|地址'=>'require',
            'county|地址'=>'require',
            'tel|公司电话' => 'require|max:20',
            'property_company|物业公司名称' => 'require|max:50',
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

//                '__token__'     => 'token',
        ];

        $validate->rule($rules);

        $model = new \app\common\model\Manager();
        try{
            if (!$post['yuan_id'] && !$post['tou_id']) throw new ValidateException('影投和院线必须至少选一个');

            $model->startTrans();

            if(!$validate->check($post))  throw new ValidateException($validate->getError());

            $service = new Service((new \app\common\typeCode\manager\Cinema()));

            if($service->existsUsername($post['username'],$post['id']))
            {
                throw new ValidateException('账户已存在');
            }

            $cinemaData = (new \app\common\service\Manager((new \app\common\typeCode\manager\Cinema())))->get($post['id']);

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
//            return json(['code'=>0]);
            $service->update($post['id'],$post);

            $service->updateInfo($oldUser['info_id'],$post);

            $user = (new \app\common\service\Manager())->get($post['id']);

            if (isset($post['level_name'])){
                //新增影院相关级别
                $levels = $post['level_name'];
                $options = $post['level_option'];

                (new CategoryObjHaveAttr(1))->update($user['group_code'],$levels,$options);
            }


            if (isset($post['area_around'])){

                $cateObjHaveService = new CategoryObjHave((new \app\common\typeCode\cateObjHave\Cinema()));

                $areaAroundArr = [];

                foreach ($post['area_around'] as $k => $v){

                    $aroundTemp = explode('-',$v);

                    $areaAroundArr[] = [
                        'cate_id'   => $aroundTemp[0],
                        'cate_name' => $aroundTemp[1]
                    ];
                }

                $cateObjHaveService->update($areaAroundArr,(new CinemaNearby()),$cinemaData['group_code']);
            }

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

    public function changeStatus(Request $request)
    {
        $status = $request->post('status');
        $id = $request->post('id');

        (new Service((new \app\common\typeCode\manager\Cinema())))->changeStatus($id,$status);

        return json(['code'=>1,'msg'=>'success']);
    }

    public function info(Request $request)
    {
        $id = $request->param('id');

        return (new \app\common\controller\Cinema())->getInfoPage($id);

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
