<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/27
 * Time: 16:43
 */

namespace app\common\service;


class Manager
{
    public function getManagerList($page=false)
    {
        $manager = new \app\common\model\Manager();
        return $page ? $manager->alias('a')->join('role b','a.role_id=b.id','left')->field('a.*,b.role_name')->order('id asc')->paginate($page) : $manager->alias('a')->join('role b','a.role_id=b.id','left')->order('id asc')->field('a.*,b.role_name')->select();
    }

    public function insert($data)
    {
        $dataRes = [
            'username'=>$data['username'],
            'password'=>md5($data['password']),
            'role_id'=>$data['role_id']
        ];

        return (new \app\common\model\Manager())->add($dataRes);
    }

    public function getFindData($id)
    {
        return (new \app\common\model\Manager())->get($id);
    }

    public function updateRes($data)
    {
        $updateData = [
            'username' => $data['username'],
            'role_id' => $data['role_id'],
        ];
        if(!empty($data['password'])) $updateData['password'] = md5($data['password']);

        return (new \app\common\model\Manager())->modify($data['id'],$updateData);
    }

    public function delete($id)
    {
        $managerData = (new \app\common\model\Manager())->get($id);
        if($managerData['role_id']==0) return ['code'=>0,'msg'=>'超级管理员不可删除'];
        $rmResult = (new \app\common\model\Manager())->rm($id);
        if(!$rmResult) return ['code'=>0,'msg'=>'删除失败'];
        return ['code'=>1,'msg'=>'success'];
    }
}