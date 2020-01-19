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
    protected $order = [];

    //描述类
    private $managerImpl = null;

    //信息表
    private $infoTableName = 'manager_info';

    //组 code
    private $groupCode = 0;

    //如果展示多个type的数据 可传入这个数组 如果数组中有值 则不会采用managerImpl中的值
    private $types = [];    //这个有问题 不能穿多个type数据 要用下面啊的

    private $typeString = '';

    private $where = [];

    private $whereIn = [];

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

    public function setOrder($field,$order)
    {
        $this->order[0] = $field;
        $this->order[1] = $order;

        return $this;
    }

    public function setGroupCode($groupCode)
    {
        $this->groupCode = $groupCode;

        return $this;
    }

    public function setTypes($type)
    {
        $this->types[] = $type;

        return $this;
    }

    public function setTypeString($type)
    {
        $this->typeString = $type;

        return $this;
    }

    public function setWhere($alias,$field,$value)
    {
        $this->where[0] = $alias;
        $this->where[1] = $field;
        $this->where[2] = $value;

        return $this;
    }

    public function getManagerImpl()
    {
        return $this->managerImpl;
    }

    public function getShowType()
    {
        return $this->showType;
    }

    public function setWhereIn($field,$value)
    {
        $this->whereIn[0] = $field;
        $this->whereIn[1] = $value;

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

        $handler = $this->showType ? $handler->backgroundShowData($alias) : $handler->receptionShowData($alias);

        $handler = $handler->alias($alias);

        $handler = count($this->order) ? $handler->order($alias.'.'.$this->order[0],$this->order[1]) : $handler;

        $handler = $this->groupCode ? $handler->where([$alias.'.group_code'=>$this->groupCode]) : $handler;

        if (!empty($this->types)){
            $handler = $handler->whereIn($alias.'.type',$this->types);
        }elseif (!empty($this->typeString)){
            $handler = $handler->whereIn($alias.'.type',$this->typeString);
        } else{
            $handler = $this->managerImpl ? $handler->where([$alias.'.type'=>$this->managerImpl->getManagerType()]) : $handler;
        }

        $handler = $handler->leftJoin($this->infoTableName.' info',$alias.'.info_id = info.id')
            ->field('*,info.id none_id,'.$alias.'.id id,info.type info_type');


        $handler = $this->where ? $handler->where($this->where[0].'.'.$this->where[1],$this->where[2]) : $handler;

        return $this->pageLength ? $handler->paginate(['list_rows'=>$this->pageLength,'query'=>request()->param()]) : $handler->select();
    }

    public function getInfoList()
    {
        $handler = new ManagerModel();

        $alias = 'manager';

        $handler = $this->showType ? $handler->backgroundShowData($alias) : $handler->receptionShowData($alias);

        $handler->alias($alias);

        $handler = $this->groupCode ? $handler->where([$alias.'.group_code'=>$this->groupCode]) : $handler;

        if (!empty($this->types)){
            $handler = $handler->whereIn($alias.'.type',$this->types);
        }elseif (!empty($this->typeString)){
            $handler = $handler->whereIn($alias.'.type',$this->typeString);
        } else{
            $handler = $this->managerImpl ? $handler->where([$alias.'.type'=>$this->managerImpl->getManagerType()]) : $handler;
        }

        $handler->join($this->infoTableName.' info',$alias.'.info_id = info.id')
            ->field('*,info.id none_id,'.$alias.'.id id,info.type info_type');

        $handler->join('manager m2','m2.id = '.$alias.'.group_code and m2.id = '.$alias.'.id');

        $handler = $this->order ? $handler->order($alias.'.'.$this->order[0],$alias.'.'.$this->order[1]) : $handler;

        $handler = $this->where ? $handler->where($this->where[0].'.'.$this->where[1],$this->where[2]) : $handler;

        $handler = $this->whereIn ? $handler->whereIn($this->whereIn[0],$this->whereIn[1]) : $handler;

        return $this->pageLength ? $handler->paginate(['list_rows'=>$this->pageLength,'query'=>request()->param()]) : $handler->select();
    }

    public function existsUsername($username,$exceptId = 0)
    {

        $handler = (new ManagerModel());

        if ($exceptId) $handler = $handler->where('id','<>',$exceptId);

//        $result = $handler->where(['type'=>$this->managerImpl->getManagerType(),'username'=> $username])->find();

        $result = $handler->where(['username'=> $username])->find();

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
            'role_name'   => $data['role_name'] ?? '',
            'group_code'  => $data['group_code'] ?? 0,
            'info_id'   => $data['info_id'] ?? 0,
            'create_time' => $createTime,
        ];

        $managerModel = (new ManagerModel());
        $managerInfoModel = new ManagerInfoModel();

        if ($this->managerImpl->isInfo() && !isset($data['info_id'])){

            $data['add_time'] = $createTime;
            $data['create_time'] = $createTime;

            $fieldArr = $this->managerImpl->getInfoField();

            $insert = [];

            foreach ($fieldArr as $key => $value) {
                if (isset($data[$value])) $insert[$value] = $data[$value];
            }

            $insert['type'] = $this->managerImpl->getManagerType();

            $managerInfoModel->insert($insert);

            $infoId = $managerInfoModel->getLastInsID();

            if (!$managerInsert['info_id']) $managerInsert['info_id'] = $infoId;
        }

        $managerModel->insert($managerInsert);

        $data['id'] = $managerModel->getLastInsID();

        if ($this->managerImpl->isInfo() && !isset($data['info_id'])){

            $id = $managerModel->getLastInsID();

            $managerInfoModel->where(['id'=>$infoId])->update(['master_user_id'=>$id]);

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
            'role_id'   => $data['role_id'] ?? 0,
            'role_name'   => $data['role_name'] ?? '',
        ];

        if (isset($data['password']) && $data['password']) $update['password'] = md5($data['password'] . $salt);
        if (isset($data['username'])) $update['username'] = $data['username'];

        $update['type'] = $this->managerImpl->getManagerType();

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

        if(isset($data['box_office_for_year'])) $update['box_office_for_year'] = $data['box_office_for_year'];
        if(isset($data['ticket_price_for_average'])) $update['ticket_price_for_average'] = $data['ticket_price_for_average'];
        if(isset($data['watch_mv_sum'])) $update['watch_mv_sum'] = $data['watch_mv_sum'];
        if(isset($data['seat_sum'])) $update['seat_sum'] = $data['seat_sum'];
        if(isset($data['screen_sum'])) $update['screen_sum'] = $data['screen_sum'];

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

    public function getInfoByGroupCode($groupCode)
    {
        return (new ManagerInfoModel())->where(['master_user_id'=>$groupCode])->find();

    }

    public function delete($id)
    {
        (new ManagerModel())->softDelete($id);
    }

    /**
     * 查询某资源方下的全部影院总数 厅总数 座位总数
     * @param $id           int   资源方id
     * @param $type         int   类型  1 影投 2 院线
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * $data 2019/12/3 13:15
     */
    public function getCinemaAmountCount($id)
    {
        $ManagerInfoModel = new  ManagerInfoModel();

        $ManagerInfoModel = $ManagerInfoModel->where('type',$this->managerImpl->getManagerType())->where(function ($query) use($id) {
            $query->where('tou_id',$id)->whereOr('yuan_id',$id);
        });

        return $ManagerInfoModel->field('count(*) as cinemaCount,sum(screen_sum) as screenSum,sum(seat_sum) as seatSum')->select();
    }

    /**
     * 查询某资源方下的直系影院总数
     * @param $id       int   资源方id
     * @return int
     * $data 2019/12/4 15:24
     */
    public function getLinealCinemaAmountCount($id)
    {
        $ManagerInfoModel = new  ManagerInfoModel();

        $ManagerInfoModel = $ManagerInfoModel->where('type',$this->managerImpl->getManagerType())->where('yuan_id',$id);

        return $ManagerInfoModel->count('id');
    }
}