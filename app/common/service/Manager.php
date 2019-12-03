<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/27
 * Time: 16:43
 */

namespace app\common\service;


use app\common\typeCode\ManagerImpl;
use app\common\model\Manager as ManagerModel;
use app\common\model\ManagerInfo as ManagerInfoModel;


class Manager
{

    //查询分页长度 如果 null则全取
    private $pageLength = null;

    //查询展示类型 如果false 被冻结的一些数据都不展示
    private $showType = false;

    //排序
    private $order = [];

    //描述类
    private $managerImpl = null;

    private $infoTableName = 'manager_info';


    public function __construct(?ManagerImpl $managerImpl = null)
    {
        $this->managerImpl = $managerImpl;
    }

    public function showType($showType = false)
    {
        $this->showType = $showType;

        return $this;
    }

    public function pageLength($pageLength = 15)
    {
        $this->pageLength = $pageLength;

        return $this;
    }

    public function order($field,$order)
    {
        $this->order[0] = $field;
        $this->order[1] = $order;

        return $this;
    }

    /**
     * 包含info 全部返回
     * @return mixed
     */
    public function getList()
    {
        $handler = new ManagerModel();

        $alias = 'manager';

        $handler = $handler->alias($alias);

        $handler = $this->showType ? $handler->backgroundShowData($alias) : $handler->receptionShowData($alias);


        $handler = $this->managerImpl ? $handler->where([$alias.'.type'=>$this->managerImpl->getManagerType()]) : $handler;

        $handler = $handler->leftJoin($this->infoTableName.' info',$alias.'.info_id = info.id')
            ->field('*,info.id none_id,'.$alias.'.id id');

        $handler = $this->order ? $handler->order($alias.'.'.$this->order[0],$alias.'.'.$this->order[1]) : $handler;

        return $this->pageLength ? $handler->paginate($this->pageLength) : $handler->select();
    }

    public function existsUsername($username,$exceptId = 0)
    {

        $handler = (new ManagerModel());

        if ($exceptId) $handler = $handler->where('id','<>',$exceptId);

        $result = $handler->where(['type'=>$this->managerImpl->getManagerType(),'username'=> $username])->find();

        return  $result ? $result : null;
    }

    public function insert($data)
    {
        if (!($this->managerImpl instanceof ManagerImpl)){
            throw new \Exception('请在构造器中传入'.ManagerImpl::class);
        }

        //创建盐值
        $salt = md5(mt_rand(10000000000,99999999999));

        $createTime = time();

        $managerInsert = [
            'username'  => $data['username'],
            'salt'      => $salt,
            'password'  => md5($data['password'] . $salt),
            'type'      => $this->managerImpl->getManagerType(),
            'role_id'   => $data['role_id'] ?? 0,
            'group_code'  => $data['group_code'] ?? 0,
            'info_id'   => $data['info_id'] ?? 0,
            'create_time' => $createTime,
        ];

        $managerModel = (new ManagerModel());
        $managerInfoModel = new ManagerInfoModel();

        if ($this->managerImpl->isInfo()){

            $data['add_time'] = $createTime;
            $data['create_time'] = $createTime;

            $fieldArr = $this->managerImpl->getInfoField();

            $insert = [];

            foreach ($fieldArr as $key => $value) {
                $insert[$value] = $data[$value];
            }

            $managerInfoModel->insert($insert);

            $infoId = $managerInfoModel->getLastInsID();

            if (!$managerInsert['info_id']) $managerInsert['info_id'] = $infoId;
        }

        $managerModel->insert($managerInsert);

        if ($this->managerImpl->isInfo()){

            $id = $managerModel->getLastInsID();

            if (!isset($data['group_code'])){
                $data['group_code'] = $id;
                $managerModel->where(['id'=>$id])->update(['group_code'=>$id]);
            }

        }
        return $data;

    }

    public function update($id,$data)
    {

        $managerModel = (new ManagerModel());

        $salt = $managerModel->where(['id'=>$id])->value('salt');

        $update = [
            'password'  => md5($data['password'] . $salt),
            'role_id'   => $data['role_id'] ?? 0,
        ];

        if (isset($data['username'])) $update['username'] = $data['username'];


        $managerModel->modify($id,$update);
    }

    public function updateInfo($infoId,$data)
    {
        if (!($this->managerImpl instanceof ManagerImpl)){
            throw new \Exception('请在构造器中传入'.ManagerImpl::class);
        }

        $managerInfoModel = new ManagerInfoModel();


        $fieldArr = $this->managerImpl->getInfoField();

        $update = [];

        foreach ($fieldArr as $key => $value) {
            if (isset($data[$value])) $update[$value] = $data[$value];
        }

        $managerInfoModel->where(['id'=>$infoId])->update($update);
    }

    public function changeStatus($id,$status)
    {
        (new ManagerModel())->modify($id,['status'=>$status]);
    }

    public function get($id)
    {
        return (new ManagerModel())->get($id);
    }

    public function getInfo($infoId)
    {
        return (new ManagerInfoModel())->get($infoId);
    }

    public function delete($id)
    {
        (new ManagerModel())->softDelete($id);
    }

}