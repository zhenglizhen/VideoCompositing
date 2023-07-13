已实现
* 网站应用上进行发布视频到快手
  支持平台
* 快手
* 抖音
  要求
  1.PHP>=7.3

2.Composer

安装
  composer require caitui/videocompositing

用法

  //快手

use VideoCompositing\Client;

    public function __construct()
    {
        $this->appName = 'kuaishou';//应用名称
        $this->appId = '**';//APPID
        $this->appSecret = '**';//秘钥
        $this->redirectUri = 'https://***';//回调地址

    }
    //获取code
    public function getCode()
    {
        $video = new Client('kuaishou');
        $params = [
            'appId' => $this->appId,
            'loginType' => 'url',//url;dynamicQR;staticQR
            'redirectUri' => $this->redirectUri,
        ];
        $video->getCode($params);
    }

    //获取AccessToken
    public function getAccessToken()
    {
        $video = new Client('kuaishou');
        $params = [
            'appId' => $this->appId,
            'appSecret' => $this->appSecret,
            'code' => ''
        ];
        $res = $video->getAccessToken($params);
    }
    //刷新AccessToken
    public function refreshAccessToken()
    {
        $video = new Client($this->appName);
        $params = [
            'appId' => $this->appId,
            'appSecret' => $this->appSecret,
            'refresh_token' => '***'
        ];
        $res = $video->refreshAccessToken($params);
    }

    //开始上传视频时必须先获取uploadToken和endpoint
    public function getUploadToken()
    {
        $video = new Client($this->appName);
        $params = [
            'appId' => $this->appId,
            'accessToken' => '****'
        ];
        $video->getUploadToken($params);
    }

    //上传视频（视频小于5M直接上传文件，大于5M则分片上传）
    public function uploadVideo(Request $request)
    {
        $file = $request->file('file');
        $uploadToken = '****';
        $endpoint = "****";

        $video = new Client($this->appName);
        $params = [
            'endpoint' => $endpoint,
            'uploadToken' => $uploadToken,
            'file' => $file,
        ];
        $video->uploadVideo($params);
    }
    
    //分片上传时查看上传失败的分片（不常用）
    public function resumeVideo()
    {
        $uploadToken = '****';
        $endpoint = "****";
        $video = new Client($this->appName);
        $params = [
            'endpoint' => $endpoint,
            'uploadToken' => $uploadToken,
        ];
        $video->resumeVideo($params);
    }

    //发布视频
    public function releaseVideo()
    {
        $accessToken = '****';
        $uploadToken = '****';
        $cover = '';//视频封面图片url
        $caption = "";//视频标题

        $video = new Client($this->appName);
        $params = [
            'appId' => $this->appId,
            'uploadToken' => $uploadToken,
            'accessToken' => $accessToken,
            'cover' => $cover,
            'caption' => $caption,
        ];

        $video->releaseVideo($params);
    }
    //获取授权用户所发布的视频列表
    public function getVideo()
    {
        $accessToken = '';
        $video = new Client($this->appName);
        $params = [
            'appId' => $this->appId,
            'accessToken' => $accessToken
        ];
        $video->getVideo($params);
    }
