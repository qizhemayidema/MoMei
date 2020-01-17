<?php


namespace app\common\service;

use app\common\model\UserRecord as UserRecordModel;
use app\common\typeCode\UserRecordImpl;

class UserRecord
{
    private $userId = '';

    private $pageLength = null;

    public function setUserId($value)
    {
        $this->userId = $value;
        return $this;
    }

    public function pageLength($pageLength = 15)
    {
        $this->pageLength = $pageLength;

        return $this;
    }

    public function getList(UserRecordImpl $obj)
    {
        $handler = new UserRecordModel();

        $handler = $handler->where('type',$obj->getType());

        $handler = $this->userId ? $handler->where('user_id',$this->userId) : $handler;

        return $this->pageLength ? $handler->paginate(['list_rows'=>$this->pageLength,'query'=>request()->param()]) : $handler->select()->toArray();
    }

    public function addData(UserRecordImpl $obj,$data)
    {
        $addData = [
            'user_id'=>$data['user_id'],
            'type'=>$obj->getType(),
            'object_id'=>$data['object_id'],
        ];
        return (new UserRecordModel())->add($addData);
    }

    /**
     * 查询某用户有没有收藏过
     * @param UserRecordImpl $obj
     * @param $userId
     * @param $objectId
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * $data 16/1/2020 下午3:39
     */
    public function g·etData(UserRecordImpl $obj,$userId,$objectId)
    {
        return (new UserRecordModel())->where('type',$obj->getType())->where('user_id',$userId)->where('object_id',$objectId)->find();
    }

    /**
     * 取消收藏
     * @param UserRecordImpl $obj
     * @param $userId
     * @param $objectId
     * @return bool
     * @throws \Exception
     * $data 16/1/2020 下午4:46
     */
    public function deleteByUserId(UserRecordImpl $obj,$userId,$objectId)
    {
        return (new UserRecordModel())->where('type',$obj->getType())->where('user_id',$userId)->where('object_id',$objectId)->delete();
    }
}