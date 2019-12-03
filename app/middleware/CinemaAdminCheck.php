<?php
declare (strict_types = 1);

namespace app\middleware;

use app\common\tool\Session;

class CinemaAdminCheck
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        if(!(new Session())->getData()){
            return redirect('/cinemaAdmin/Login/index');
        }
        return $next($request);
    }
}
