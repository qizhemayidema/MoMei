<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/25
 * Time: 14:17
 */

namespace app\common\tool;


class Config
{
    private $object = null;

    private $path = null;

    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * 获取配置信息
     * @param $name
     * @return mixed|null
     * 使用 a.b.c
     */
    public function getConfig($name)
    {
        if ($name == '*') return json_decode(file_get_contents($this->path),true);
        if (!$this->object){
            $this->configObject = json_decode(file_get_contents($this->path));
        }
        $configPath = explode('.', $name);
        $temp = $this->object;
        foreach ($configPath as $key => $value) {
            $temp = $temp->$value;
        }
        if ($temp === null) throw new \Exception('获取配置失败');

        $temp = json_decode(json_encode($temp,256),true);

        return $temp;
    }
}