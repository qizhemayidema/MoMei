<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/25
 * Time: 17:29
 */

namespace app\bAdmin\controller;

use app\bAdmin\controller\Base;
use app\common\service\Permission;

class AuthRule extends Base
{
    public function index(){
        try{
            //查询全部的权限
            $ruleArr = (new Permission())->getRuleList();
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }
}