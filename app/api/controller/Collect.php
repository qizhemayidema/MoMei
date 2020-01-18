<?php
declare (strict_types = 1);

namespace app\api\controller;

use app\common\service\Category;
use app\common\service\CategoryObjHave;
use app\common\service\CategoryObjHaveAttr;
use app\common\service\Manager as ManagerServer;
use app\common\service\UserRecord as UserRecordServer;
use app\common\typeCode\cate\CinemaNearby;
use app\common\typeCode\record\Collect as CollectDesc;
use think\Exception;
use think\Request;

class Collect extends Base
{

    public function getCollectCinemaList()
    {
        $user = $this->userInfo;

        //查询此用户收藏的店铺
        $cinemas = (new UserRecordServer())->setUserId($user['id'])->getList(new CollectDesc());
        $result = [];
        if(!empty($cinemas)){
            //查询全部的收藏的店铺的数据
            $groupCodes = array_column($cinemas,'object_id');
            $service = new \app\common\model\Manager();
            $dataList = $service->alias('manager')->join('manager_info info','manager.info_id = info.id')
                ->field('*,info.id none_id,manager.id id,info.type info_type')->join('manager m2','m2.id = manager.group_code and m2.id = manager.id')
                ->where(['manager.type'=>4,'manager.delete_time'=>0,'manager.status'=>1])
                ->whereIn('manager.group_code',$groupCodes)
                ->select()->toArray();
            if(!empty($dataList)){
                //获取级别分类列表  这里是需要返回影院的级别的选项
                $cateService = new Category();
                $level = $cateService->getList((new \app\common\typeCode\cate\CinemaLevel()));
                foreach ($level as $levelKey => $levelValue){
                    $level[$levelKey]['attr'] =  $cateService->getAttrList($levelValue['id'])->toArray();
                }
                $levelCheck = (new CategoryObjHaveAttr(1))->getList();
                $newLevelCheck = [];
                foreach ($levelCheck as $levelCheckKey=>$levelCheckValue){
                    $newLevelCheck[$levelCheckValue['object_id']][$levelCheckValue['cate_id']] = $levelCheckValue['attr_id'];
                }

                //获取影院的周边选择
                $cinemaEarByTypeCode = (new CinemaNearby());
                //获取全部的影院的周边的选择
                $selectAroundList = (new CategoryObjHave((new \app\common\typeCode\cateObjHave\Cinema())))->getListAll($cinemaEarByTypeCode)->toArray();

                //组装级别   周边区域
                $result = ['code'=>1,'msg'=>'success','data'=>[]];
                foreach ($dataList as $key=>$value){
                    $cinemaLevel = [];
                    $rim = [];

                    //级别
                    if(isset($newLevelCheck[$value['id']])){
                        foreach ($level as $levelKey=>$levelValue){
                            if(isset($newLevelCheck[$value['id']][$levelValue['id']])){
                                foreach ($levelValue['attr'] as $levelKey1=>$levelValue2){
                                    if($newLevelCheck[$value['id']][$levelValue['id']] == $levelValue2['id']){
                                        $cinemaLevel[$levelValue['name']]=$levelValue2['value'];
                                    }
                                }
                            }
                        }
                    }
                    //区域周边
                    foreach ($selectAroundList as $selectAroundListKey=>$selectAroundListValue){
                        if($selectAroundListValue['object_id']==$value['id']){
                            $rim[] = $selectAroundListValue['cate_name'];
                        }
                    }

                    $result['data'][$key]['level'] = $cinemaLevel;
                    $result['data'][$key]['rim'] = $rim;
                    $result['data'][$key]['province'] = $value['province'];
                    $result['data'][$key]['city'] = $value['city'];
                    $result['data'][$key]['county'] = $value['county'];
                    $result['data'][$key]['desc'] = $value['desc'];
                    $result['data'][$key]['pic'] = explode(',',$value['pics'])[0];
                    $result['data'][$key]['id'] = $value['id'];
                    $result['data'][$key]['cinema_name'] = $value['name'];
                }
            }
        }

        return json(['code'=>1,'msg'=>'success','data'=>$result]);
    }

    /**
     * 收藏店铺
     * @param Request $request
     * @return \think\response\Json
     * $data 16/1/2020 下午2:35
     */
    public function collectCinema(Request $request)
    {
        try{
            $user = $this->userInfo;

            $CollectDesc = new CollectDesc();

            $cinemaId = $request->post('cinema_id'); //实则传过来的也是group_code

            if(!$cinemaId) throw new \Exception('参数错误');

            //查询店铺是否存在
            $cinemaRes = (new ManagerServer())->get($cinemaId);

            $cinemaEarByTypeCode = (new CinemaNearby());
            if(empty($cinemaRes) || $cinemaRes['delete_time']!=0 || $cinemaRes['type']!=$cinemaEarByTypeCode->getCateType()) throw new \Exception('店铺不存在');

            if($cinemaRes['status']!=1) return json(['code'=>0,'msg'=>'影院已下线']);

            $isCollect = (new UserRecordServer())->getData($CollectDesc,$user['id'],$cinemaId);

            if(!empty($isCollect)) throw new \Exception('您已经收藏');

            $data = [
                'user_id' =>$user['id'],
                'object_id' =>$cinemaId,
            ];

            $result = (new UserRecordServer())->addData((new CollectDesc()),$data);

            if(!$result) throw new \Exception('收藏失败');

            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 取消收藏的店铺
     * @param Request $request
     * @return \think\response\Json
     * $data 16/1/2020 下午3:22
     */
    public function deleteCollectCinema(Request $request)
    {
        try{
            $user = $this->userInfo;

            $cinemaId = $request->post('cinema_id'); //实则传过来的也是group_code

            if(!$cinemaId) throw new \Exception('参数错误');

            $result = (new UserRecordServer())->deleteByUserId((new CollectDesc()),$user['id'],$cinemaId);

            if(!$result) throw new \Exception('取消失败');

            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
}
