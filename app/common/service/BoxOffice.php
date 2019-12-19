<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/12/4
 * Time: 13:33
 */

namespace app\common\service;


use app\common\model\BoxOfficeStatistics as BoxOfficeStatisticsModel;

class BoxOffice
{
    private $order = [];

    private $pageLength = null;

    private $where = [];

    private $groupCode = 0;


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

    public function getList($cinemaid=false,$times=false)
    {
        $handler = new BoxOfficeStatisticsModel();

        $handler = $this->order ? $handler->order($this->order[0],$this->order[1]) : $handler;

        $handler = $this->groupCode ? $handler->where(['cinema_id'=>$this->groupCode]) : $handler;

        $handler = $this->where ? $handler->where($this->where[0],$this->where[1]) : $handler;

        if($cinemaid) $handler = $handler->where(['cinema_id'=>$cinemaid]);

        if($times){
            $start_time = strtotime($times);
            $end_time = strtotime(date('Y-m-d 23:59:59',$start_time));
            $handler = $handler->where('create_time','between',[$start_time,$end_time]);
        }


        return $this->pageLength ? $handler->paginate($this->pageLength) : $handler->select();
    }

    public function get($id){
        return (new BoxOfficeStatisticsModel())->get($id);
    }

    public function insert($data)
    {

        $addData = [
            'cinema_id'=>$data['group_code'],
            'cinema_name'=>$data['cinema_name'],
            'tou_id'=>$data['tou_id'] ?? 0,
            'yuan_id'=>$data['yuan_id'] ?? 0,
            'number_value'=>$data['number_value'],
            'income_value'=>$data['income_value'],
            'create_time'=>time()
        ];
        return (new BoxOfficeStatisticsModel())->add($addData);
    }

    public function update($data){
        $updateData = [
            'number_value'=>$data['number_value'],
            'income_value'=>$data['income_value'],
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
        $handler = new BoxOfficeStatisticsModel();

        $todayStart = strtotime(date('Y-m-d').' 00:00:00');
        $todayEnd = strtotime(date('Y-m-d').' 23:59:59');

        return  $handler->where('cinema_id',$groupCode)->where('create_time','between',[$todayStart,$todayEnd])->find();
    }
}