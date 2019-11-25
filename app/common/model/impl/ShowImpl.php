<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/25
 * Time: 10:13
 */

namespace app\common\model\impl;


interface ShowImpl
{
    /**
     * 前台可展示数据条件
     * @param  $alias string 别名
     * @return mixed
     * return $this
     */
    public function receptionShowData(string $alias);

    /**
     * 后台可展示数据条件
     * @param  $alias string 别名
     * @return mixed
     * return $this
     */
    public function backgroundShowData(string $alias);
}