<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/23
 * Time: 15:25
 */

namespace app\common\tool;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use think\facade\Cache;

class Sms
{
    private $accessKeyId = null;

    private $accessKeySecret = null;

    private $signName = null;

    private $templateCode = null;

    private $validTime = null;   //有效时间

    private $cachePrefix = null;  //缓存前缀

    private $config = [
        'maxRequestNum' => 25,      //每个手机号每天最多请求次数
    ];

    public function __construct($envPrefix,$cachePrefix = '',$validTime = 300)
    {
        $this->accessKeyId = env($envPrefix.'.ACCESS_KEY_ID');
        $this->accessKeySecret = env($envPrefix.'.ACCESS_KEY_SECRET');
        $this->signName = env($envPrefix.'.SIGN_NAME');
        $this->templateCode = env($envPrefix.'.TEMPLATE_CODE');

        $this->validTime = $validTime;

        $this->cachePrefix = $cachePrefix;
    }

    //获取短信
    public function sendCodeForPhone($phone)
    {

        if (Cache::has($this->cachePrefix.$phone)){
            $after = Cache::get($this->cachePrefix.$phone)['timestamp'];
            if (time() - $after <= $this->validTime){
                return ['code'=>0,'msg'=>'短信已发信,请耐心等待'];
            }
        }
        $checkArr = $this->requestCheck();

        if ($checkArr['code'] == 0) return $checkArr;

        $result = $this->sendPhoneCode($phone);

        if ($result['code']){
            //记录发送时间 记录手机号
            $this->requestLog();
            return ['code'=>1,'msg'=>'success'];
        }else{
            return ['code'=>0,'msg'=>'发送失败,请联系网站管理员'];
        }
    }

    //检查验证码是否有效
    public function checkPhoneCode($phone,$code){

         $oldCode = Cache::get($this->cachePrefix.$phone);

         if ($oldCode['code'] != $code) return false;

         return true;

    }

    /*发送验证码*/
    private function sendPhoneCode($phone)
    {
        $code = mt_rand(100000,999999);

        AlibabaCloud::accessKeyClient($this->accessKeyId, $this->accessKeySecret)->asDefaultClient();

        try {
            $result = AlibabaCloud::rpc()
                ->regionId('cn-beijing')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'PhoneNumbers'  => $phone,
                        'SignName'  => $this->signName,
                        'TemplateCode'  => $this->templateCode,
                        'TemplateParam' => json_encode(['code'=>$code]),
                    ],
                ])
                ->request();

            $res = $result->toArray();

            if($res['Code']!='OK') return ['code'=>0,'data'=>'发送失败'];

            Cache::set($this->cachePrefix.$phone,['code'=>$code,'timestamp'=>time()]);
            return ['code'=>1,'data'=>$code];
        } catch (ClientException $e) {
//            echo $e->getErrorMessage() . PHP_EOL;
            return ['code'=>0,'msg'=>$e->getErrorMessage()];
        } catch (ServerException $e) {
            return ['code'=>0,'msg'=>$e->getErrorMessage()];
        }
    }

    private function requestCheck()
    {
        if ($ip = $this->getIP()){
            $ipRequestNum = Cache::get($this->cachePrefix.$ip);
            if ($ipRequestNum){
                if ($ipRequestNum > $this->config['maxRequestNum']){
                    return ['code'=>0,'msg'=>'同一ip24小时内只能获取'.$this->config['maxRequestNum'].'次验证码'];
                }else{
                    Cache::inc($this->cachePrefix.$ip);
                }
            }else{
                Cache::set($ip,0,86400);
            }
        }

        return ['code'=>1,'msg'=>'success'];
    }

    private function requestLog()
    {
        if ($ip = $this->getIP()){
            $ipRequestNum = Cache::get($this->cachePrefix.$ip);
            if ($ipRequestNum){
                Cache::inc($this->cachePrefix.$ip);
            }else{
                Cache::set($ip,0,86400);
            }
        }

    }

    private function getIP()
    {
        $ip = false;
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if(getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if(getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        return $ip;
    }
}