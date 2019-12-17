<?php
declare (strict_types = 1);

namespace app\bAdmin\controller;

use app\BaseController;
use app\common\model\ManagerInfo;
use app\common\service\Manager as Service;
use app\common\service\Category as CateService;
use app\common\tool\Upload;
use app\common\typeCode\cate\ABus as ABusTypeDesc;
use app\common\typeCode\cate\ABus;
use app\common\typeCode\manager\Yuan;
use app\common\typeCode\manager\Ying;
use app\common\typeCode\manager\Cinema as CinemaTypeDesc;
use think\exception\ValidateException;
use think\Validate;
use think\facade\View;
use think\Request;

class AUser extends Base
{
    public function index()
    {
        $yuan = new Yuan();
        $ying = new Ying();
        $list = (new Service())->setTypes($ying->getManagerType())->setTypes($yuan->getManagerType())->showType(true)->order('id','desc')->pageLength(15)->getList();

        View::assign('list',$list);

        return view();

    }

    public function info(Request $request)
    {
        $id = $request->param('id');

        $service = (new Service(new CinemaTypeDesc()));


        $user = $service->get($id);

        $info = $service->getInfo($user['info_id']);

        //查询影院关联总数等
        $countResult  = $service->getCinemaAmountCount($user['group_code']);

//        //查询直系影院总数
//        $getLinealCinemaAmountCount = $service->getLinealCinemaAmountCount($info['info_id']);
//
//        View::assign('getLinealCinemaAmountCount',$getLinealCinemaAmountCount);

        View::assign('countResult',$countResult);

        View::assign('user',$user);

        View::assign(['info'=>$info]);

        return view();

    }

    public function add()
    {
        //获取a端行业分类
        $busCate = (new CateService())->getList((new ABusTypeDesc()));

        View::assign('bus_cate',$busCate);

        return view();
    }

    public function save(Request $request)
    {
        $post = $request->post();

        $validate = new Validate();

        $rules = [
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

        $model = new \app\common\model\Manager();
        try{
            $model->startTrans();

            if(!$validate->check($post))  throw new ValidateException($validate->getError());

            if ($post['type'] == 2){
                $service = new Service((new Yuan()));
            }else{
                $service = new Service((new Ying()));
            }

            if($service->existsUsername($post['username'])){
                throw new ValidateException('账户已存在');
            }

            $post['pro_name'] = (new \app\common\model\Category())->get($post['pro_id'])['name'];

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

        $service = (new Service());

        $user = $service->get($id);

        $data = $service->getInfo($user['info_id']);

        //获取a端行业分类
        $busCate = (new CateService())->getList((new ABusTypeDesc()));

        View::assign('bus_cate',$busCate);

        View::assign('user',$user);

        View::assign('data',$data);

        return view();

    }

    public function update(Request $request)
    {
        $post = $request->post();


        $validate = new Validate();

        $rules = [
            'id' => 'require',
            'username|账户名' => 'require|max:32',
//            'password|密码' => '',
            're_password|确认密码' => 'confirm:password',
            'address|公司详细地址' => 'require|max:128',
            'bus_license|营业执照' => 'require|max:1000',
            'bus_license_code|营业执照代码' => 'require|max:100',
            'province|地址' => 'require',
            'city|地址' => 'require',
            'county|地址' => 'require',
            'tel|公司电话' => 'require|max:20',
            'contact|联系人姓名' => 'require|max:30',
            'contact_license_code|联系人身份证号' => 'require|max:18',
            'contact_license_pic|联系人身份证照片' => 'require|max:500',
            'contact_sex|联系人性别' => 'require',
            'contact_tel|联系人电话' => 'require|max:20',
            'contact_wechat|联系人微信' => 'require|max:127',
            'credit_code|统一社会信用代码' => 'require|max:18',
            'email|工作邮箱' => 'require|email',
            'name|企业名称' => 'require|max:31',
            'pro_id|行业分类' => 'require',
            'type|账号类型' => 'require',
//                '__token__'     => 'token',
        ];


        $validate->rule($rules);

        $model = new \app\common\model\Manager();

        try {
            $model->startTrans();

            if (!$validate->check($post)) throw new ValidateException($validate->getError());

            if ($post['type'] == 2){
                $typeCode = new Yuan();
            }else{
                $typeCode =  new Ying();
            }

            $service = new Service($typeCode);

            if ($service->existsUsername($post['username'], $post['id'])) {
                throw new ValidateException('账户已存在');
            }

            $oldUser = $service->get($post['id']);

            $post['role_id'] = $oldUser['role_id'];

            $post['pro_name'] = (new \app\common\model\Category())->get($post['pro_id'])['name'];

            $service->update($post['id'], $post);

            $service->updateInfo($oldUser['info_id'],$post);

            $model->commit();

            return json(['code' => 1, 'msg' => 'success']);
        } catch (ValidateException $e) {

            $model->rollback();
            return json(['code' => 0, 'msg' => $e->getMessage()]);

        } catch (\Exception $e) {

            $model->rollback();
            return json(['code' => 0, 'msg' => $e->getMessage()]);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->post('id');

        (new Service())->delete($id);

        return json(['code'=>1,'msg'=>'success']);
    }

    public function associatedCinema(Request $request)
    {
        $param = $request->param();

        //查询属于该资源方下的影院
        $managerService = '';
        $field = '';
        $managerService = new Service(new CinemaTypeDesc());
        if($param['type']==2){  //院线
            $field = 'yuan_id';
        }elseif ($param['type']==3){ //影投
            $field = 'tou_id';
        }

        $data = $managerService->setWhere('info',$field,$param['id'])->showType(true)->pageLength()->getInfoList();

        View::assign('data',$data);

        return view();
    }

    public function changeStatus(Request $request)
    {
        $status = $request->post('status');

        $id = $request->post('id');

        (new Service())->changeStatus($id,$status);

        return json(['code'=>1,'msg'=>'success']);
    }

//    public function getProduct(Request $request)
//    {
//        $type = $request->post('type');
//
//        $data = (new CateService())->getListByType($type);
//
//        return json(['code'=>1,'data'=>$data]);
//
//    }


    public function uploadPic()
    {
        return json((new Upload())->uploadOnePic('aUser/'));
    }


}
