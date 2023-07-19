###已实现
* 网站应用上进行发布视频到快手
###支持平台
* 快手
* 抖音
* 微信公众号
###要求
1.PHP>=7.3

2.Composer

###安装
    composer require caitui/videocompositing
###用法（快手）
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
    public function uploadVideo()
    {
        $file = '****';//视频url
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

###用法（公众号）
    use VideoCompositing\Client;

    public function __construct()
    {
        $this->appName = 'wechat';//应用名称
        $this->appId = '**';//APPID
        $this->appSecret = '**';//秘钥
    }
    public function getAccessToken()
    {
        $client = new Client($this->appName);
        $params = [
            'appid' => $this->appId,
            'secret' => $this->appSecret,
        ];
        $res = $client->getAccessToken($params);
    }
    //发布内容
    public function createContent()
    {
        $client = new Client($this->appName);
        $params = [
            'access_token' => '****',
            'media' => '***',//封面图
            'content'=>'****',//内容
            'title'=>'测试标题',//标题
            'author'=>'',//作者
            'digest'=>'',//图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空。如果本字段为没有填写，则默认抓取正文前54个字。
            'content_source_url'=>'',//图文消息的原文地址，即点击“阅读原文”后的URL
            'need_open_comment'=>1,//是否打开评论，0不打开(默认)，1打开
            'only_fans_can_comment'=>0//是否粉丝才可评论，0所有人可评论(默认)，1粉丝才可评论
        ];
        $res = $client->createContent($params);
    }

###用法（抖音）
    protected $clientKey; //抖音key
    protected $clientSecret; //抖音secret
    protected $appName; //应用 kuaishou/douyin
    protected $redirectUri; //回调链接

    public function __construct()
    {
        $this->appName = 'douyin';
        $this->clientKey = '***';
        $this->clientSecret = '****';
        $this->redirectUri = '****';
    }
    
    //获取code值
    public function getCode()
    {
        $client = new Client($this->appName);
        $params = [
            'clientKey' => $this->clientKey,
            'redirectUri' => $this->redirectUri
        ];
        $client->getCode($params)
    }
    
    //获取accessToken和openid（access_token 过期时间15天，refresh_token过期时间30天）
    public function getAccessToken()
    {
        $video = new Client($this->appName);
        $params = [
            'clientKey' => $this->clientKey,
            'clientSecret' => $this->clientSecret,
            'code' => '5b80487773db1dc7df4aog83v7J2T9pjN2H1'
        ];
        $res = $video->getAccessToken($params);
    }

    //刷新AccessToken
    public function refreshAccessToken()
    {
        $video = new Client($this->appName);
        $params = [
            'client_key' => $this->clientKey,
            'refresh_token' => 'rft.b7ea117a9656655c773786311312b2c4SyKcq5B4rYS3uZb2bprgR81utX8f'
        ];
        $video->refreshAccessToken($params);
    }

    //上传图文 获取image_id
    public function uploadImage()
    {
        $file = '***';//图片链接
        $video = new Client($this->appName);
        $accessToken = '***';
        $openId = '***';
        $params = [
            'accessToken' => $accessToken,
            'openId' => $openId,
            'image' => $file,
        ];
        $res = $video->uploadImage($params);*
    }
    
    //发布图文
    public function createImage()
    {
        $video = new Client($this->appName);
        $accessToken = '***';
        $openId = '***';
        $text = '测试'; 标题
        $image_list = ['***', '***']; // 多个image_id
        $params = [
            'accessToken' => $accessToken,
            'openId' => $openId,
            'text' => $text,
            'image_list' => $image_list
        ];
        $res = $video->createImage($params);
    }

    //上传视频 获取video_id （视频小于10M直接上传。大于10M则分片上传）
    public function uploadVideo()
    {
        $file = '****';//视频url
        $video = new Client($this->appName);
        $accessToken = '***';
        $openId = '***';
        $params = [
            'accessToken' => $accessToken,
            'openId' => $openId,
            'video' => $file,
        ];
        $res = $video->uploadVideo($params);
    }
    public function createVideo()
    {
        $video = new Client($this->appName);
        $accessToken = '***';
        $openId = '***';
        $text = '测试'; //标题
        $video_id = '***';
        $params = [
            'accessToken' => $accessToken,
            'openId' => $openId,
            'text' => $text,
            'video_id' => $video_id
        ];
        $res = $video->createVideo($params);
    }