<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/28
 * Time: 15:57
 */

namespace app\common\tool;


class Session
{
    private  $sessionPath = null;


    public function __construct()
    {
        $this->sessionPath = config('app.session_path');
    }

    /**
     * @return mixed|null
     */
    public function getSessionPath(): ?string
    {
        return $this->sessionPath;
    }

    /**
     * @param mixed|null $sessionPath
     */
    public function setSessionPath(?string $sessionPath): void
    {
        $this->sessionPath = $sessionPath;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return \think\facade\Session::get($this->sessionPath);
    }

    /**
     * @param null $data
     */
    public function setData($data): void
    {
        \think\facade\Session::set($this->sessionPath,$data);
    }
}