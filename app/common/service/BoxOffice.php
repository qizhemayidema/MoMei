<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/12/4
 * Time: 13:33
 */

namespace app\common\service;


use app\common\model\BoxOfficeStatistics as BoxOfficeStatisticsModel;
use app\common\typeCode\BoxOfficeImpl;

class BoxOffice
{
    private $boxOfficeImpl = null;

    private $order = [];

    private $pageLength = null;

    private $where = [];

    private $groupCode = 0;

    public function __construct(?BoxOfficeImpl $boxOfficeImpl = null)
    {
        $this->boxOfficeImpl = $boxOfficeImpl;
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

    public function setWhere($field,$value)
    {
        $this->where[0] = $field;
        $this->where[1] = $value;

        return $this;
    }

    public function setGroupCode($groupCode)
    {
        $this->groupCode = $groupCode;

        return $this;
    }

    public function getList()
    {
        $handler = new BoxOfficeStatisticsModel();

        $handler = $this->order ? $handler->order($this->order[0],$this->order[1]) : $handler;

        $handler = $this->groupCode ? $handler->where(['cinema_id'=>$this->groupCode]) : $handler;

        $handler = $this->where ? $handler->where($this->where[0],$this->where[1]) : $handler;

        $handler = $this->boxOfficeImpl ? $handler->where(['type'=>$this->boxOfficeImpl->getBoxType()]) : $handler;


        return $this->pageLength ? $handler->paginate($this->pageLength) : $handler->select();
    }

    public function get($id){
        return (new BoxOfficeStatisticsModel())->get($id);
    }

    public function insert($data)
    {
        if (!($this->boxOfficeImpl instanceof BoxOfficeImpl)){
            throw new \Exception('请在构造器中传入'.boxOfficeImpl::class);
        }

        $addData = [
            'cinema_id'=>$data['group_code'],
            'cinema_name'=>$data['cinema_name'],
            'tou_id'=>$data['tou_id'] ?? 0,
            'yuan_id'=>$data['yuan_id'] ?? 0,
            'type'=>$this->boxOfficeImpl->getBoxType(),
            'value'=>$data['value'],
            'create_time'=>time()
        ];
        return (new BoxOfficeStatisticsModel())->add($addData);
    }

    public function update($data){
        $updateData = [
            'value'=>$data['value'],
        ];

        return (new BoxOfficeStatisticsModel())->modify($data['id'],$updateData);
    }

    public function delete($id)
    {
        return (new BoxOfficeStatisticsModel())->rm($id);
    }

    /**
     * 查询某影院今日有没有添加过票房收入  观影人数
     * @param $groupCode
     * @return array|null|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * $data times
     */
    public function getToday($groupCode)
    {
        if (!($this->boxOfficeImpl instanceof BoxOfficeImpl)){
            throw new \Exception('请在构造器中传入'.boxOfficeImpl::class);
        }

        $handler = new BoxOfficeStatisticsModel();

        $todayStart = strtotime(date('Y-m-d').' 00:00:00');
        $todayEnd = strtotime(date('Y-m-d').' 23:59:59');

        return  $handler->where('type',$this->boxOfficeImpl->getBoxType())->where('cinema_id',$groupCode)->where('create_time','between',[$todayStart,$todayEnd])->find();
    }
}