<?php

namespace VideoCompositing;

use VideoCompositing\Application\DouYin;
use VideoCompositing\Application\KuaiShou;

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
     * 刷新token 有效期
     * @return mixed|string
     */
    public function refreshAccessToken()
    {
        return (new KuaiShou())->refreshAccessToken($params);
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

    /**
     * 快手获取视频
     * @param $params
     * @return mixed|string
     */
    public function getVideo($params)
    {
        return (new KuaiShou())->getVideo($params);
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
     * 抖音创建视频
     * @return mixed|string
     */
    public function createVideo($params)
    {
        return (new DouYin())->createVideo($params);
    }
}