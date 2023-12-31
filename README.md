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
    
    //获取用户信息
    public function getUserInfo()
    {
        $video = new Client('kuaishou');
        $params = [
            'appId' => $this->appId,
            'accessToken' => '****'
        ];
        $res = $video->getUserInfo($params);
        var_dump($res);
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
    public function uploadVideo(\app\Request $request)
    {
        $file = $request->file('file');
        $fileSize=$file->getSize();
        $uploadToken = '****';
        $endpoint = "****";

        //$file = 'https://myy-one-stand.oss-cn-beijing.aliyuncs.com/list/2033/202307/1689735980720.mp4';
       // $fileSize = $video->getVideoSize($file);
        //$save_to = app()->getRootPath() . '/public/code/1.mp4';
        //$content = file_get_contents($file);
        //file_put_contents($save_to, $content);
        //$file = $save_to;

        $video = new Client($this->appName);
        $params = [
            'endpoint' => $endpoint,
            'uploadToken' => $uploadToken,
            'file' => $file,
            'fileSize'=>$fileSize
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
    //获取授权用户所发布的视频列表(需分页)
    public function getVideo()
    {
        $accessToken = '';
        $video = new Client($this->appName);
        $params = [
            'appId' => $this->appId,
            'accessToken' => $accessToken,
            'count' => 10,//每页条数
            'cursor' => 0//第一页为0,分页查询时，传上一页create_time最小的photo_id
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

    //获取用户基本信息和粉丝数
    public function getUserInfo()
    {
        $accessToken = '****';
        $openId = '****';
        $video = new Client($this->appName);
        $params = [
            'accessToken' => $accessToken,
            'openId' => $openId,
        ];
        $res = $video->getUserInfo($params);
        var_dump($res);
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
    public function uploadImage(\app\Request $request)
    {
        $file = $request->file('file');
        
        //或者将url转存到本地
        //$save_to = dirname(dirname(__DIR__)) . '/1.jpg';
        //$content = file_get_contents('https://myy-one-stand.oss-cn-beijing.aliyuncs.com/company/2117/5019-1687141391.jpg');
        //file_put_contents($save_to, $content);
        //$file = $save_to;

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
    public function uploadVideo(\app\Request $request)
    {
        $file = $request->file('file');
        $fileSize = $file->getSize();
        $video = new Client($this->appName);

        //视频链接处理
        //$file = 'https://myy-one-stand.oss-cn-beijing.aliyuncs.com/list/2033/202307/1689735980720.mp4';
        //$fileSize = $video->getVideoSize($file);
        //$save_to = dirname(dirname(__DIR__)) . '/1.mp4';
        //$content = file_get_contents($file);
        //file_put_contents($save_to, $content);
        //$file = $save_to;


        $accessToken = 'act.3.h227zKK0gByaIXPZWnvynsNM5BfsqNrAbTwe9eGZxOZhl0JuWQB3VtU0qu2rTmKb6WmAeqLqbvqX8Jsy01yHquSm8g2YlTxH5lG-ontiOJ1tWbeqgOpx71GRFvTW5x8eX2U10EtCKqjB0pwn1m0C8dXnKAVevJWxtgo6cA==';
        $openId = '_000_8_Z3n99pcaq6SzKKmmLmMjpJyf7RFf1';
        $params = [
        'accessToken' => $accessToken,
        'openId' => $openId,
        'video' => $file,
        ];

        
        $maxFileSize = 1048576 * 10;

        if ($fileSize < $maxFileSize) { //文件小于10M直接上传文件
            $params['fileSize'] = $fileSize;
            $res = $video->uploadVideo($params);
            var_dump($res);
        }else{
            $upload_id = $video->initDistribute($params);
            $upload_id = urlencode($upload_id);

            $file_handle = fopen($file, 'rb');
            $part_number = 1;
            $total = floor($fileSize / $maxFileSize);
            if ($part_number == $total) {
                $maxFileSize = 1048576 * 20;
            }
            while (!feof($file_handle)) {
                // 读取指定大小的数据
                $chunk_data = fread($file_handle, $maxFileSize);
                $params['upload_id'] = $upload_id;
                $params['part_number'] = $part_number;

                $save_to = '/a' . $params['part_number'] . '.mp4';
                file_put_contents($save_to, $chunk_data);
                $params['video'] = $save_to;
                $video->distributeVideo($params);
                // 增加分片编号
                $part_number++;
            }
            fclose($file_handle);
            var_dump($video->completeDistribute($params));
        }
    }

    //发布视频
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

    //获取用户视频详细信息（视频列表，视频点赞、浏览、评论等数据）
    public function getVideoData()
    {
        $video = new Client($this->appName);
        $accessToken = '****';
        $openId = '****';
        $params = [
            'accessToken' => $accessToken,
            'openId' => $openId,
            'date_type'=>7 //7/15 点赞、浏览、评论等列表数据只能查询出近7/15天内
        ];
        $res = $video->getVideoData($params);
        var_dump($res);
    }

    //获取近7/15/30天的用户数据（播放量，点赞量，评论数，粉丝数，分享数，主页访问数）
    public function getUserData()
    {
        $video = new Client($this->appName);
        $accessToken = '****';
        $openId = '****';
        $params = [
            'accessToken' => $accessToken,
            'openId' => $openId,
            'date_type'=>7 //7/15
        ];
        $res = $video->getUserData($params);
        var_dump($res);
    }

    //获取用户发布的视频列表（类型，封面，视频播放页面，是否置顶，创建时间，点赞数、下载数、分享数、播放数、评论数）
    //返回数据中的has_more可以判定是否有更多的数据
    public function getVideo()
    {
        $video = new Client($this->appName);
        $accessToken = '****';
        $openId = '****';
        $params = [
            'accessToken' => $accessToken,
            'openId' => $openId,
            'cursor' => 0,//分页游标，第一页为0，下一页为上一页返回的cursor；
            'count'=>10, //每页条数
        ];
        $res = $video->getVideo($params);
    }
    

    //获取多个视频的视频详情，评论数、点赞数、下载数等数据
    public function getVideoDetail()
    {
        $video = new Client($this->appName);
        $accessToken = '****';
        $item_ids = ['****', '****']; //元素为item_id值
        $openId = '****';
        $params = [
            'accessToken' => $accessToken,
            'item_ids' => $item_ids,
            'openId' => $openId,
        ];
        $res = $video->getVideoDetail($params);
        var_dump($res);
    }

    //获取某个视频的近7/15/30天的点赞、评论、分享、播放数据
    //like：点赞数据；comment：评论数据；play：播放数据；share：分享数据；
    public function getVideoData()
    {
        $video = new Client($this->appName);
        $accessToken = '****';
        $item_id = '****';
        $openId = '****';
        $params = [
            'accessToken' => $accessToken,
            'item_id' => $item_id,
            'openId' => $openId,
            'date_type'=>7,//7/15
        ];
        $res = $video->getBaseData($params);
    }