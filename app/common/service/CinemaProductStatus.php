<?php
/**
 * Created by : PhpStorm
 * User: fk
 * Date: 18/1/2020
 * Time: 下午2:38
 */
namespace app\common\service;

use app\common\model\CinemaProductStatus as CinemaProductStatusModel;
class CinemaProductStatus
{
    /**
     * 修改产品的某段时间的档期
     * @param $startTime
     * @param $endTime
     * @param $entityIds
     * @param $status
     * @return CinemaProductStatusModel
     * $data 18/1/2020 下午3:28
     */
    public function updateByTimes($startTime,$endTime,$entityIds,$status)
    {
        return (new CinemaProductStatusModel())->where('date','between',[$startTime,$endTime])->whereIn('entity_id',$entityIds)->update(['status'=>$status]);
    }

    //查询某个时间段的商品有多少天的档期
    public function productCalendarDay($startTime,$endTime,$id)
    {
        return (new CinemaProductStatusModel())->where('date','between',[$startTime,$endTime])->where('status',0)->where('entity_id',$id)->count();
    }
}