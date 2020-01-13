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
}