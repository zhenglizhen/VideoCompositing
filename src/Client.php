<?php

namespace VideoCompositing;

use VideoCompositing\Application\DouYin;
use VideoCompositing\Application\KuaiShou;
use VideoCompositing\Application\WeChat;

class Client
{
    private $appName;

    public function __construct($appName)
    {
        $this->appName = $appName;
    }

    /**
     * 获取code
     * @param $params
     * @return string
     */
    public function getCode($params)
    {
        if (!is_array($params)) return '参数错误！';
        switch ($this->appName) {
            case 'kuaishou':
                return (new KuaiShou())->getCode($params);
                break;
            case 'douyin':
                return (new DouYin())->getCode($params);
            default:
                return '正在开发中';
        }

    }

    /**
     * 获取token
     * @param $params
     * @return mixed|string
     */
    public function getAccessToken($params)
    {
        switch ($this->appName) {
            case 'kuaishou':
                return (new KuaiShou())->getAccessToken($params);
                break;
            case 'douyin':
                return (new DouYin())->getAccessToken($params);
            case 'wechat':
                return (new WeChat())->getAccessToken($params);
            default:
                return '正在开发中';
        }

    }

    /**
     * 根据token获取用户信息
     * @param $params
     * @return mixed|string
     */
    public function getUserInfo($params)
    {
        switch ($this->appName) {
            case 'kuaishou':
                return (new KuaiShou())->getUserInfo($params);
                break;
            case 'douyin':
                $data = [];

                //用户粉丝数
                $params['url'] = 'fans';
                $res = (new DouYin())->getUserBaseData($params);
                $res = array_reverse($res);
                $data['fans'] = $res[0]['total_fans'];

                $res = (new DouYin())->getUserInfo($params);
                $data['avatar'] = $res['data']['avatar'];
                $data['nickname'] = $res['data']['nickname'];
                $data['open_id'] = $res['data']['open_id'];
                return $data;
            default:
                return '正在开发中';
        }

    }


    /**
     * 上传视频
     * @param $params
     * @return mixed|string
     */
    public function uploadVideo($params)
    {
        switch ($this->appName) {
            case 'kuaishou':
                return (new KuaiShou())->uploadVideo($params);
                break;
            case 'douyin':
                return (new DouYin())->uploadVideo($params);
            default:
                return '正在开发中';
        }

    }

    /**
     * 获取视频
     * @param $params
     * @return mixed|string
     */
    public function getVideo($params)
    {
        switch ($this->appName) {
            case 'kuaishou':
                return (new KuaiShou())->getVideo($params);
            case 'douyin':
                return (new DouYin())->getVideo($params);
            default:
                return '正在开发中';
        }
    }

    /**
     * 刷新token 有效期
     * @return mixed|string
     */
    public function refreshAccessToken($params)
    {
        switch ($this->appName) {
            case 'kuaishou':
                return (new KuaiShou())->refreshAccessToken($params);
            case 'douyin':
                return (new DouYin())->refreshAccessToken($params);
            default:
                return '正在开发中';
        }
    }

    /**
     * 快手获取uploadToken
     * @param $params
     * @return mixed|string
     */
    public function getUploadToken($params)
    {
        return (new KuaiShou())->getUploadToken($params);
    }

    /**
     * 快手断点续传（此接口查询已经上传的分片,从失败的分片重新上传）
     * @param $params
     * @return mixed|string
     */
    public function resumeVideo($params)
    {
        return (new KuaiShou())->resumeVideo($params);
    }

    /**
     * 快手发布视频
     * @param $params
     * @return mixed|string
     */
    public function releaseVideo($params)
    {
        return (new KuaiShou())->releaseVideo($params);
    }

    public function getVideoData2($params)
    {
        $res = (new KuaiShou())->getVideo($params);
        if ($res['result'] != 1) {
            return $res;
        }
        $data['count'] = count($res['video_list']);
        $data['like_count'] = 0;
        $data['comment_count'] = 0;
        $data['view_count'] = 0;
        foreach ($res['video_list'] as $v) {
            $data['like_count'] = $data['like_count'] + $v['like_count'];
            $data['comment_count'] = $data['comment_count'] + $v['comment_count'];
            $data['view_count'] = $data['view_count'] + $v['view_count'];
        }
        return $data;
    }

    /**
     * 抖音创建图文
     * @param $params
     * @return mixed|string
     */
    public function createImage($params)
    {
        return (new DouYin())->createImage($params);
    }

    /**
     * 抖音上传图文
     * @param $params
     * @return mixed|string
     */
    public function uploadImage($params)
    {
        return (new DouYin())->uploadImage($params);
    }

    /**
     * 上传视频
     * @param $params
     * @return mixed|string
     */
    public function initDistribute($params)
    {
        return (new DouYin())->initDistribute($params);
    }

    public function distributeVideo($params)
    {
        return (new DouYin())->distributeVideo($params);
    }

    public function completeDistribute($params)
    {
        return (new DouYin())->completeDistribute($params);
    }

    public function getVideoSize($url)
    {
        return (new DouYin())->getVideoSize($url);
    }

    /**
     * 抖音创建视频
     * @return mixed|string
     */
    public function createVideo($params)
    {
        return (new DouYin())->createVideo($params);
    }

    /**
     * 查看视频详情
     */
    public function getVideoDetail($params)
    {
        return (new DouYin())->getVideoData($params);
    }

    public function getBaseData($params)
    {
        $data = [];
        $params['url'] = 'like';
        $res = (new DouYin())->getBaseData($params)['data'];
        if ($res['error_code']) {
            return $res['description'];
        }
        $data['like'] = $res['result_list'];

        $params['url'] = 'comment';
        $res = (new DouYin())->getBaseData($params)['data'];
        if ($res['error_code']) {
            return $res['description'];
        }
        $data['comment'] = $res['result_list'];

        $params['url'] = 'play';
        $res = (new DouYin())->getBaseData($params)['data'];
        if ($res['error_code']) {
            return $res['description'];
        }
        $data['play'] = $res['result_list'];

        $params['url'] = 'share';
        $res = (new DouYin())->getBaseData($params)['data'];
        if ($res['error_code']) {
            return $res['description'];
        }
        $data['share'] = $res['result_list'];
        return $data;
    }

    /**
     * 抖音获取视频数据
     * @param $params
     * @return mixed
     */
    public function getVideoData($params)
    {
        $list = (new DouYin())->getVideo($params);

        if ($list['data']['error_code']) {
            return $list['data'];
        }
        $item_ids = [];
        foreach ($list['data']['list'] as $k => &$v) {
            $params['item_id'] = $v['item_id'];

            $params['url'] = 'like';
            $res = (new DouYin())->getBaseData($params)['data'];
            if (!$res['error_code']) {
                $v['like_data'] = (new DouYin())->getBaseData($params)['data']['result_list'];
            } else {
                $v['like_data'] = [];
            }

            $params['url'] = 'comment';
            $res = (new DouYin())->getBaseData($params)['data'];
            if (!$res['error_code']) {
                $v['comment_data'] = (new DouYin())->getBaseData($params)['data']['result_list'];
            } else {
                $v['comment_data'] = [];
            }

            $params['url'] = 'play';
            $res = (new DouYin())->getBaseData($params)['data'];
            if (!$res['error_code']) {
                $v['play_data'] = (new DouYin())->getBaseData($params)['data']['result_list'];
            } else {
                $v['play_data'] = [];
            }

            $params['url'] = 'share';
            $res = (new DouYin())->getBaseData($params)['data'];
            if (!$res['error_code']) {
                $v['share_data'] = (new DouYin())->getBaseData($params)['data']['result_list'];
            } else {
                $v['share_data'] = [];
            }
        }
        return $list['data']['list'];
    }

    /**
     * 抖音获取用户数据
     */
    public function getUserData($params)
    {
        $data = [];
        $params['url'] = 'item';
        $data['item'] = (new DouYin())->getUserBaseData($params);

        $params['url'] = 'fans';
        $data['fans'] = (new DouYin())->getUserBaseData($params);

        $params['url'] = 'like';
        $data['like'] = (new DouYin())->getUserBaseData($params);

        $params['url'] = 'comment';
        $data['comment'] = (new DouYin())->getUserBaseData($params);

        $params['url'] = 'share';
        $data['share'] = (new DouYin())->getUserBaseData($params);

        $params['url'] = 'profile';
        $data['profile'] = (new DouYin())->getUserBaseData($params);

        return $data;
    }

    public function getFansData($params)
    {
        $params['url']='data';
        return (new DouYin())->getFansBaseData($params);
    }

    /**
     * 微信公众号创建内容
     * @param $params
     */
    public function createContent($params)
    {
        if (!isset($params['access_token']) || empty($params['access_token'])) {
            return 'access_token不能为空';
        }
        if (!isset($params['title']) || empty($params['title'])) {
            return 'title不能为空';
        }
        if (!isset($params['content']) || empty($params['content'])) {
            return 'content不能为空';
        }
        if (!isset($params['media']) || empty($params['media'])) {
            return 'media不能为空';
        }

        //上传封面图
        $res = (new WeChat())->uploadMedia($params);
        $params['thumb_media_id'] = $res['media_id'];//封面图的media_id

        //创建草稿
        $res = (new WeChat())->createDraft($params);
        $params['media_id'] = $res['media_id'];//草稿的media_id

        $res = (new WeChat())->publish($params);
        return $res;
    }
}