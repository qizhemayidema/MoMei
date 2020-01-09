<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/18
 * Time: 13:28
 */

namespace app\common\service;


use app\common\typeCode\CateImpl;
use app\common\typeCode\CateObjHaveImpl;

class CategoryObjHave
{
    private $objectTypeImpl = null;   //1 影院 2 地区

    public function __construct(?CateObjHaveImpl $impl = null)
    {
        $this->objectTypeImpl = $impl;
    }

    public function getList(CateImpl $impl,$objectId)
    {
        return (new \app\common\model\CategoryObjHave())->where([
            'object_id'=>$objectId,
            'object_type'=>$this->objectTypeImpl->getObjType(),
            'cate_type'=>$impl->getCateType()
        ])->select();
    }
    public function getListAll(CateImpl $impl)
    {
        return (new \app\common\model\CategoryObjHave())->where([
            'object_type'=>$this->objectTypeImpl->getObjType(),
            'cate_type'=>$impl->getCateType()
        ])->select();
    }

    public function insertAll($data,CateImpl $impl,$objectId)
    {
        $insert = [];

        foreach ($data as $k => $v){
            $insert[] = [
                'cate_id'   => $v['cate_id'],
                'cate_name' => $v['cate_name'],
                'cate_type' => $impl->getCateType(),
                'object_type' => $this->objectTypeImpl->getObjType(),
                'object_id' => $objectId,
            ];
        }

        (new \app\common\model\CategoryObjHave())->insertAll($insert);
    }

    /**
     * @param $data [cate_id,cate_name]
     * @param CateImpl $impl
     * @param $objectId
     * @throws \Exception
     */
    public function update($data,CateImpl $impl,$objectId)
    {
        $model = new \app\common\model\CategoryObjHave();

        //用户留下的 cate_id
        $dataIds = array_column($data,'cate_id');

        $deleteArr = [];

        $updateArr = [];

        $insertArr = [];

        //查询之前选择的分类
        $result =  $model->where(['object_id'=>$objectId,'object_type'=>$this->objectTypeImpl->getObjType(),'cate_type'=>$impl->getCateType()])->column('cate_id','id');

        //筛选将要被删除的数据和要被修改的数据
        foreach ($result as $id => $cateId){
            if (!in_array($cateId,$dataIds)){
                $deleteArr[] = $id;
            }else{
                $updateArr[] = $cateId;
            }
        }

        //筛选新增数据
        foreach ($data as $k => $v){
            if (!in_array($v['cate_id'],$result)){
                $insertArr[] = [
                    'cate_id' => $v['cate_id'],
                    'cate_name' => $v['cate_name'],
                ];
            }
        }

       $deleteArr &&  $model->whereIn('id',$deleteArr)->delete();

        //更新数据
        foreach ($data as $k => $v){
            if (in_array($v['cate_id'],$updateArr)){
                $model->where(['cate_id'=>$v['cate_id'],'object_id'=>$objectId,'object_type'=>$this->objectTypeImpl->getObjType()])
                    ->update([
                        'cate_name' => $v['cate_name'],
                    ]);
            }
        }

        //插入新增的数据
        $this->insertAll($insertArr,$impl,$objectId);
    }
}