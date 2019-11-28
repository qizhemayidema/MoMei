<?php
declare (strict_types = 1);

namespace app\bAdmin\controller;

use app\BaseController;
use app\common\service\Category as CateService;
use app\common\tool\Upload;
use app\common\typeCode\cate\ABus as TypeDesc;
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
        //获取一级城市的数据
//        (new Cate)

        //获取a端行业分类
        $busCate = (new CateService())->getList((new TypeDesc()));

        View::assign('bus_cate',$busCate);

        return view();
    }

    public function uploadPic()
    {
        $path = 'bus/';
        return json((new Upload())->uploadOnePic($path));
    }
}
