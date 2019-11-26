<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/26
 * Time: 15:48
 */

namespace app\common\service;


use app\common\typeCode\AUserImpl;
use app\common\model\AUser as AUserModel;

class AUser
{
    private $pageLength = 15;
    /**
     * @param AUserImpl $AUserImpl
     * @param bool $showType 是否显示全部数据(冻结 等等) true 显示  false 不显示
     */
    public function getList(AUserImpl $AUserImpl,$showType = false)
    {
        $handler = new AUserModel();

        if($showType){
            $handler = $handler->backgroundShowData();
        }else{
            $handler = $handler->receptionShowData();
        }

        return  $handler->getList($AUserImpl);
    }

    public function getListPage(AUserImpl $AUserImpl,$showType = false)
    {
        $handler = new AUserModel();

        if($showType){
            $handler = $handler->backgroundShowData();
        }else{
            $handler = $handler->receptionShowData();
        }

        return $handler->where(['type'=>$AUserImpl->getUserType()])->order('id','desc')->paginate($this->pageLength);

    }

    public function getAList($showType = false)
    {
        $handler = new AUserModel();

        if($showType){
            $handler = $handler->backgroundShowData();
        }else{
            $handler = $handler->receptionShowData();
        }

        return $handler->order('id','desc')->paginate($this->pageLength);
    }
}