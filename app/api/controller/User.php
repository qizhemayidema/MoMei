<?php
declare (strict_types = 1);

namespace app\api\controller;

use think\Request;
use think\Validate;

class User extends Base
{
    public function register()
    {
        $rules = [
            'phone|手机号'        => 'require|mobile|max:11',
            'password|密码'       => 'require|max:32',
            'name|姓名'           => 'require|max:10',
            'sex|性别'            => 'require|integer',
            'work_email|工作邮箱' => 'require|email|max:30',
            'ent_name|公司名称'     => 'require|max:128',

            'ent_type|公司类型'    => 'require',

        ];

        $validate = new Validate();

        $validate->rule($rules);


    }

    public function login()
    {

    }

    public function loginWithCode()
    {

    }
}
