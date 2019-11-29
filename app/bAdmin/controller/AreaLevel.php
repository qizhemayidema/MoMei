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
use app\common\typeCode\cate\LevelOption;
use app\Request;
use think\facade\View;
use think\Validate;

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

            return view('area_level/index');
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
            //查询级别选项的
            $cateList = (new Category())->getList(new LevelOption());

            View::assign('cateList',$cateList);
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
                'level_id|级别'=>'require',
                'level_sort|排序'=>'require|between:0,999',
                'is_hot|热门'=>'require',
                '__token__'     => 'token',
            ));

            if(!$valiatde->check($post))  throw new \Exception($valiatde->getError());

            //根据级别id查出值
            $cateOneResult = (new Category())->getOneById($post['level_id']);

            $post['level_value'] = $cateOneResult['name'];

            $result = (new Area())->update($post);
            if(!$result) throw new \Exception('修改失败');
            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
}