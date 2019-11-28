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

    private $showType = false;

    private $aUserImpl = null;



    public function __construct(AUserImpl $aUserImpl)
    {
        $this->aUserImpl = $aUserImpl;
    }

    public function setShowType($showType = false)
    {
        $this->showType = $showType;
    }

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

        return  $handler->getListByType($AUserImpl->getUserType());
    }

    /**
     * @param AUserImpl $AUserImpl
     * @param null $showType
     * @return \think\Paginator
     */
    public function getListPage(AUserImpl $AUserImpl,$showType = null)
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