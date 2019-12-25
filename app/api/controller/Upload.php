<?php
declare (strict_types = 1);

namespace app\api\controller;

use think\Request;

class Upload extends Base
{
    public function base64Img(Request $request)
    {
        $base64Content = $request->post('file');

        $result = (new \app\common\tool\Upload())->uploadBase64Pic($base64Content,'img/');

        return json($result);
    }

    public function streamImg()
    {
        return json((new \app\common\tool\Upload())->uploadOnePic('img/','file'));
    }
}
