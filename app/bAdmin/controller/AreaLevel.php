<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/27
 * Time: 9:30
 */
declare (strict_types = 1);

namespace app\bAdmin\controller;


use app\common\service\Area;
use app\common\service\Category;
use app\Request;
use think\facade\View;
use think\Validate;
use app\common\service\CategoryObjHaveAttr;

class AreaLevel extends Base
{
    public function index(Request $request)
    {
        try{
            $pid = $request->param('pid')?$request->param('pid'):0;

            //查询全部的省
            $pidResult = (new Area())->getListByPId($pid);
            if($pidResult[0]['level']==3){
                //单独查询一下上一级的id
                $getFindResult = (new Area())->getFindById($pidResult[0]['pid']);
                View::assign('threePid',$getFindResult['pid']);
            }


            View::assign('pidResult',$pidResult);

            return view();
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function edit(Request $request)
    {
        try {
            //查询城市
            $city_id = $request->param('city_id');

            $getFindResult = (new Area())->getFindById($city_id);

            //获取级别分类列表
            $cateService = new Category();

            $level = $cateService->getList((new \app\common\typeCode\cate\AreaLevel()));

            foreach ($level as $key => $value){
                $level[$key]['attr'] =  $cateService->getAttrList($value['id']);
            }

            //获取数据级别选中状态
            $levelCheck = (new CategoryObjHaveAttr(2))->getIdColumns($city_id);

            View::assign('level_check',$levelCheck);
            View::assign('level',$level);
            View::assign('data', $getFindResult);
            return view();
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function update(Request $request)
    {
        try{
            $post = $request->post();

            $valiatde = new Validate();
            $valiatde->rule(Array(
                'id'       => 'require',
                '__token__'     => 'token',
            ));

            if(!$valiatde->check($post))  throw new \Exception($valiatde->getError());

            //新增影院相关级别
            $levels = $post['level_name'];
            $options = $post['level_option'];

            (new CategoryObjHaveAttr(2))->update($post['id'],$levels,$options);

            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
}