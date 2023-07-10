<?php

namespace VideoCompositing;
use VideoCompositing\Application\KuaiShou;
class Client
{
    private $appId;
    private $appSecret;
    private $loginType;
    private $appName;
    private $redirectUri;

    public function __construct($appId, $appSecret, $loginType,$appName,$redirectUri='')
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->loginType = $loginType;
        $this->appName = $appName;
        $this->redirectUri = $redirectUri;
    }

    public function getCode()
    {
        switch ($this->appName)
        {
            case 'kuaishou':
                (new KuaiShou($this->appId, $this->appSecret, $this->loginType,$this->redirectUri))->getCode();
                break;
            default:
                    return '正在开发中';
        }
    }
}