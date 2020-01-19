<?php
declare (strict_types = 1);

namespace app\api\controller;

use app\common\service\Manager;
use think\Request;

class TheatreChain
{
    /**
     * 获取影投 院线
     * $data 13/1/2020 下午2:33
     */
    public function getList()
    {
        $server = (new Manager());
        $data = $server->setTypeString('2,3')->getInfoList()->toArray();

        $result = [];
        foreach ($data as $value){
            $result[] = [
                'id'=>$value['group_code'],
                'name'=>$value['name'],
            ];
        }

        return json(['code'=>1,'msg'=>'success','data'=>$result]);
    }
}
