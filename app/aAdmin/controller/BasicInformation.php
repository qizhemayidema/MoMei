<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/12/2
 * Time: 13:06
 */
declare (strict_types = 1);
namespace app\aAdmin\controller;

use app\common\service\Manager as ManagerService;
use app\common\service\Category as CateService;
use app\common\service\Cinema as CinemaService;
use app\common\typeCode\cate\ABus as ABusTypeDesc;
use think\facade\View;
use app\common\tool\Session;
use app\common\tool\Upload;
use app\Request;
use think\Validate;
use think\exception\ValidateException;

class BasicInformation extends Base
{
    public function edit()
    {
        $info = (new Session())->getData();

        $managerService = new ManagerService();

        $managerData = $managerService->get($info['id']);

        $managerInfoData =$managerService->getInfo($info['info_id']);

        //获取a端行业分类
        $busCate = (new CateService())->getList((new ABusTypeDesc()));

        //查询影院关联总数等
        $countResult  = (new CinemaService())->getCinemaAmountCount($info['id'],$info['type']);

        View::assign('countResult',$countResult);

        View::assign('bus_cate',$busCate);

        View::assign('managerData',$managerData);

        View::assign('managerInfoData',$managerInfoData);

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
                '__token__'     => 'token',
        ];


        $validate->rule($rules);

        $aUserModel = new \app\common\model\AUser();

        try {
            $aUserModel->startTrans();

            if (!$validate->check($post)) throw new ValidateException($validate->getError());


            $service = new Service();

            if ($service->existsUsername($post['username'], $post['type'], $post['id'])) {
                throw new ValidateException('该用户名已存在');
            }

            (new Service())->update($post['id'], $post);  //这里用户可以多填写年总票房等

            $aUserModel->commit();

            return json(['code' => 1, 'msg' => 'success']);
        } catch (ValidateException $e) {

            $aUserModel->rollback();
            return json(['code' => 0, 'msg' => $e->getMessage()]);

        } catch (\Exception $e) {

            $aUserModel->rollback();
            return json(['code' => 0, 'msg' => $e->getMessage()]);


        }
    }

    public function uploadPic()
    {
        return json((new Upload())->uploadOnePic('aUser/'));
    }
}