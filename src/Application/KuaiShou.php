<?php

namespace VideoCompositing\Application;
use VideoCompositing\Http\Client;
class KuaiShou
{
    private $appId;
    private $appSecret;
    private $loginType;
    private $redirectUri;

    public function __construct($appId, $appSecret, $loginType,$redirectUri)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->loginType = $loginType;
        $this->redirectUri = $redirectUri;
    }

    public function getCode()
    {
        switch ($this->loginType) {
            case 'url':
                $url=KuaiShow_HOST.'/oauth2/authorize?app_id='.$this->appId.'&scope=user_info&response_type=code&ua=pc&redirect_uri='.$this->redirectUri.'&ua=pc';
                break;
            case 'dynamicQR':
                $url=KuaiShow_HOST.'/oauth2/connect?app_id='.$this->appId.'&scope=user_info&response_type=code&ua=pc&redirect_uri='.$this->redirectUri;
                break;
            case 'staticQR':
                $url=KuaiShow_HOST.'/oauth2/qr_code?app_id='.$this->appId.'&scope=user_info&response_type=code&ua=pc&redirect_uri='.$this->redirectUri;
                break;
            default:
                return '参数错误！';
        }
        Client::get($url);
    }
}