<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/12/6
 * Time: 11:52
 */

namespace app\bAdmin\controller;

use app\common\service\Area;
use app\common\service\AreaConfig as AreaConfigService;
use app\common\service\AreaConfig;
use app\Request;
use think\facade\View;

class AreaHot extends Base
{
    public function edit()
    {
        //查询全部的热门城市
        $data = (new AreaConfigService())->pageLength()->getList(1);

        $area = (new Area())->getListByPId();

        View::assign(['data'=>$data,'area'=>$area]);

        return view();
    }

    public function update(Request $request)
    {
        try{
            $post = $request->post();

            if(empty($post['updata'])){
                $post['updata'][] = 0;
            }

            $areaConfigService = new AreaConfigService();
            $areaConfigService->updateNotInAll($post['updata'],['is_hot'=>2]);

            if(!empty($post['adddata'])){
                $cityAll = (new AreaConfigService())->getList(); //表中全部的存储过的城市

                $cityAllIds = [];

                if(!empty($cityAll)) $cityAllIds = array_column($cityAll,'city_id');

                $addIds = array_diff(array_unique($post['adddata']),$cityAllIds);

                if(!empty($addIds)){
                    $addCity = [];
                    foreach ($addIds as $v){
                        $addCity[]['city_id'] = $v;
                    }
                    $areaConfigService->insertAll($addCity);
                }
                $areaConfigService->updateInAll($post['adddata'],['is_hot'=>1]);
            }
            
            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function getAreaByPid(Request $request){
        try{
            $id = $request->post('id');

            $data = (new Area())->getListByPId($id);

            return json(['code'=>0,'msg'=>'success','res'=>$data]);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
}