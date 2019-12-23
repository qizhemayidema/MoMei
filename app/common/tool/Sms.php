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

    private function __construct($cachePrefix = '',$validTime = 300)
    {
        $this->accessKeyId = env('SMS_ACCESS_KEY_ID');
        $this->accessKeySecret = env('SMS_ACCESS_KEY_SECRET');
        $this->signName = env('SMS_SIGN_NAME');
        $this->templateCode = env('SMS_TEMPLATE_CODE');

        $this->validTime = $validTime;

        $this->cachePrefix = $cachePrefix;
    }

    public function getPhoneCode($phone)
    {
        if (!$phone) return ['code'=>0,'msg'=>'error1'];
        if (Cache::has($this->cachePrefix.$phone)){
            $after = Cache::get($this->cachePrefix.$phone)['timestamp'];
            if (time() - $after <= $this->validTime){
                return ['code'=>0,'msg'=>'短信已发信,请耐心等待'];
            }
        }
        $result = $this->sendPhoneMsg($phone);

        if ($result['status']){
            //记录发送时间 记录手机号
            Cache::set($this->cachePrefix.$phone,$result['code'],$this->validTime);
            return ['code'=>1,'msg'=>'success'];
        }else{
            return ['code'=>0,'msg'=>'发送失败,请联系网站管理员'];
        }
    }

    public function checkCode($phone,$code){

        $cacheCode = Cache::get($this->cachePrefix.$phone);

        return $cacheCode == $code;
    }

    /**
     * 发送短信
     * @param $phone
     * @return array
     * @throws ClientException
     */
    public function sendPhoneMsg($phone)
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
            Cache::set('');
            return ['status'=>1,'data'=>$result->toArray(),'code'=>$code];
        } catch (ClientException $e) {
//            echo $e->getErrorMessage() . PHP_EOL;
            return ['status'=>0,'msg'=>$e->getErrorMessage()];
        } catch (ServerException $e) {
            return ['status'=>0,'msg'=>$e->getErrorMessage()];
        }
    }
}