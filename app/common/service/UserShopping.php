<?php


namespace app\common\service;

use app\common\model\UserShopping as UserShoppingModel;
class UserShopping
{
    public function updateByScreenId($screenId,$screenName)
    {
        return (new UserShoppingModel())->where('screen_id',$screenId)->update(['screen_name'=>$screenName]);
    }

    /**
     * 根据 影院id 修改影院的 影院名称
     * @param $cinemaId    int   是manager表的group_code
     * @param $cinemaName
     * @return UserShoppingModel
     * $data 10/1/2020 下午4:33
     */
    public function updateByCinemaId($cinemaId,$cinemaName)
    {
        return (new UserShoppingModel())->where('cinema_id',$cinemaId)->update(['cinema_name'=>$cinemaName]);
    }

    /**
     * 根据 产品id 修改产品的 产品名称
     * @param $productId
     * @param $productName
     * @return UserShoppingModel
     * $data 10/1/2020 下午5:15
     */
    public function updateByProductId($productId,$productName)
    {
        return (new UserShoppingModel())->where('product_id',$productId)->update(['product_name'=>$productName]);
    }

    /**
     * 查询某个用户 购物车中某个时间段有没有有该产品
     * @param $uid
     * @param $productId
     * @param $startTime
     * @param $endTime
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * $data 13/1/2020 上午11:39
     */
    public function getDataByIdTimes($uid,$productId,$startTime,$endTime)
    {
        $handler = new UserShoppingModel();
        return  $handler->where('user_id',$uid)->where('product_id',$productId)->where('start_time',$startTime)->where('end_time',$endTime)->find();

    }

    /**
     * 查询出购物车中的一部分产品信息
     * @param $uid
     * @param $cartIds
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * $data 18/1/2020 上午10:30
     */
    public function getDataByInIds($uid,$cartIds)
    {
        $handler = new UserShoppingModel();
        return $handler->where('user_id',$uid)->whereIn('id',$cartIds)->select()->toArray();
    }

    /**
     * 删除购物车的商品
     * @param $uid
     * @param $ids
     * @return bool
     * @throws \Exception
     * $data 18/1/2020 下午2:27
     */
    public function deleteByIds($uid,$ids)
    {
        return (new UserShoppingModel())->where(['user_id' => $uid])->whereIn('id', $ids)->delete();
    }
}