<?php

namespace app\common\tool;

use think\facade\Filesystem;
use think\File;
use think\file\UploadedFile;
use think\Request;

class Upload
{

    private $uploadRootPath = '';

    public function __construct()
    {
        $this->uploadRootPath = config('app.upload_root_path');
    }

    /**
     * @param null $file_path 保存的目录
     * @param null $form_name 表单中的名称
     */
    public function uploadOnePic($file_path = null, $form_name = 'file')
    {
        try {
            $rules = [
                'fileSize' => 10 * 1024 * 1024,
                'fileExt' => 'jpeg,jpg,png,gif'
            ];
            $file = request()->file($form_name);

            validate(['file' => $rules])->check(['file' => $file]);

            $savePath = $this->upload($file_path, $file);

            return ['code' => 1, 'msg' => $savePath];
        } catch (\think\exception\ValidateException $e) {
            return ['code' => 0, 'msg' => '格式仅支持jpeg,jpg,png,gif,最大图片为10Mb'];
        } catch (\Exception $e) {
            return ['code' => 0, 'msg' => '操作失误,请稍后重试'];
        }


    }

    /**
     * @param null $file_path
     * @param \think\File $file
     * @return array
     */
    public function uploadOnePicForObject($file_path = null, \think\File $file)
    {
        try {
            $rules = [
                'fileSize' => 10 * 1024 * 1024,
                'fileExt' => 'jpeg,jpg,png,gif'
            ];

            validate(['file' => $rules])->check(['file' => $file]);

            $savePath = $this->upload($file_path, $file);

            return ['code' => 1, 'msg' => $savePath];

        } catch (\think\exception\ValidateException $e) {
            return ['code' => 0, 'msg' => '格式仅支持jpeg,jpg,png,gif,最大图片为10Mb'];
        } catch (\Exception $e) {
            return ['code' => 0, 'msg' => '操作失误,请稍后重试'];
        }
    }

    /**
     * @param $base64_image_content
     * @param $path
     * @return array
     */
    public function uploadBase64Pic($base64_image_content, $path)
    {
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
            $type = $result[2];
            $new_file = $this->uploadRootPath . $path . date('Ymd', time()) . "/";
            if (!file_exists('.' . $new_file)) {
                mkdir('.' . $new_file, 0777, true);
            }
            $file_name = md5(time() . mt_rand(100000000, 999999999));
            $new_file = $new_file . $file_name . ".{$type}";
            if (file_put_contents('.' . $new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))) {
                return ['code' => 1, 'msg' => $new_file];
            } else {
                return ['code' => 0, 'msg' => 'error'];
            }
        } else {
            return ['code' => 0, 'msg' => 'error1'];
        }
    }

    public function uploadOneVideo($file_path = null, $form_name = 'file')
    {
        try {

            $rules = [
                'fileSize' => 300 * 1024 * 1024,
                'fileExt' => 'mp4'
            ];

            $file = request()->file($form_name);

            validate(['file' => $rules])->check(['file' => $file]);

            $savePath = $this->upload($file_path, $file);

            return ['code' => 1, 'msg' => $savePath];

        } catch (\think\exception\ValidateException $e) {
            return ['code' => 0, 'msg' => '格式仅支持mp4,支持最大视频300Mb'];
        } catch (\Exception $e) {
            return ['code' => 0, 'msg' => '操作失误,请稍后重试'];
        }


    }

    protected function upload($file_path, \think\File $file)
    {
        $file_path = $file_path ? $this->uploadRootPath . $file_path : $this->uploadRootPath;

        if (!file_exists('.' . $file_path)) {

            mkdir('.' . $file_path, 0777, true);

        }

        $path = Filesystem::disk('public')->putFile($file_path, $file, 'uniqid');

        return Filesystem::getDiskConfig('public', 'url') . '/' . str_replace('\\', '/', $path);

    }
}