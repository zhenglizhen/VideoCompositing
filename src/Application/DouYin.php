<?php

namespace VideoCompositing\Application;

use VideoCompositing\Config;
use VideoCompositing\Http\Client;

class DouYin
{
    /**
     * 获取code
     * @param $params
     * @return string
     */
    public function getCode($params)
    {
        if (!isset($params['clientKey']) || empty($params['clientKey'])) {
            return 'clientKey不能为空';
        }
        if (!isset($params['redirectUri']) || empty($params['redirectUri'])) {
            return 'redirectUri不能为空';
        }
        $url = Config::DouYin_HOST . '/platform/oauth/connect?client_key=' . $params['clientKey'] . '&response_type=code&scope=user_info,h5.share,im.share,trial.whitelist&redirect_uri=' . $params['redirectUri'] . '&state=STATE';
        header("Location: $url");
        exit();
    }

    /**
     * 获取AccessToken
     * @param $params
     * @return mixed|string
     */
    public function getAccessToken($params)
    {
        if (!isset($params['clientKey']) || empty($params['clientKey'])) {
            return 'clientKey不能为空';
        }
        if (!isset($params['clientSecret']) || empty($params['clientSecret'])) {
            return 'clientSecret不能为空';
        }
        if (!isset($params['code']) || empty($params['code'])) {
            return 'code不能为空';
        }
        $url = Config::DouYin_HOST . '/oauth/access_token';
        $body = [
            'client_secret' => $params['clientSecret'],
            'code' => $params['code'],
            'grant_type' => 'authorization_code',
            'client_key' => $params['clientKey'],
        ];
        $res = Client::POST($url, $body);
        return json_decode($res->body, true);
    }

    /**
     * 上传图片（用于创建图文视频）
     * @param $params
     * @return mixed|string
     */
    public function uploadImage($params)
    {
        if (!isset($params['accessToken']) || empty($params['accessToken'])) {
            return 'accessToken不能为空';
        }
        if (!isset($params['openId']) || empty($params['openId'])) {
            return 'openId不能为空';
        }
        if (!isset($params['image']) || empty($params['image'])) {
            return 'image不能为空';
        }
        $url = Config::DouYin_HOST . '/api/douyin/v1/video/upload_image/?open_id=' . $params['openId'];
        $body = [
            'image' => $params['image']
        ];
        $header = [
            'access-token' => $params['accessToken']
        ];
        $res = Client::POST($url, $body, $header);
        return json_decode($res->body, true);
    }

    /**
     *创建图文抖音
     * @return mixed|string
     */
    public function createImage($params)
    {
        if (!isset($params['accessToken']) || empty($params['accessToken'])) {
            return 'accessToken不能为空';
        }
        if (!isset($params['openId']) || empty($params['openId'])) {
            return 'openId不能为空';
        }
        if (!isset($params['image_list']) || empty($params['image_list'])) {
            return 'image_list不能为空';
        }
        $url = Config::DouYin_HOST . '/api/douyin/v1/video/create_image_text/?open_id=' . $params['openId'];
        $body = [
            'image_list' => $params['image_list'],
            'text' => $params['text'] ?? '',
        ];
        $header = [
            'access-token' => $params['accessToken']
        ];
        $res = Client::POST($url, $header, $body);
        return json_decode($res->body, true);
    }

    /**
     * 上传视频
     * @param $params
     * @return mixed|string
     */
    public function uploadVideo($params)
    {
        if (!isset($params['accessToken']) || empty($params['accessToken'])) {
            return 'accessToken不能为空';
        }
        if (!isset($params['openId']) || empty($params['openId'])) {
            return 'openId不能为空';
        }
        if (!isset($params['video']) || empty($params['video'])) {
            return 'video不能为空';
        }
        $fileSize = $params['video']->getSize();

        $maxFileSize = 1048576 * 100;
        if ($fileSize < $maxFileSize) {//文件小于100M直接上传文件
            $url = Config::DouYin_HOST . '/api/douyin/v1/video/upload_video/?open_id=' . $params['openId'];
            $body = [
                'video' => $params['video']
            ];
            $header = [
                'access-token' => $params['accessToken']
            ];
            $res = Client::POST($url, $body, $header);
            return json_decode($res->body, true);
        } else {//分片上传
            $upload_id = $this->initDistribute($params);
            $file_handle = fopen($params['video'], 'rb');
            $part_number = 1;
            while (!feof($file_handle)) {
                // 读取指定大小的数据
                $chunk_data = fread($file_handle, $maxFileSize);
                $params['upload_id'] = $upload_id;
                $params['part_number'] = $part_number;
                $params['video'] = $chunk_data;
                $this->distributeVideo($params);
                // 增加分片编号
                $part_number++;
            }
            fclose($file_handle);
            $this->completeDistribute($params);
        }
    }

    /**
     * 分片上传初始化
     * @param $params
     */
    public function initDistribute($params)
    {
        $url = Config::DouYin_HOST . '/api/douyin/v1/video/init_video_part_upload/?open_id=' . $params['openId'];
        $header = [
            'access-token' => $params['accessToken']
        ];
        $res = Client::post($url, [], $header);
//        var_dump('fragment_id:' . $params['chunk_number']);
//        var_dump(json_decode($res->body, true));
        $res = (json_decode($res->body, true));
        return $res['upload_id'];
//        return json_decode($res->body, true);
    }

    /**
     * 分片上传
     * @param $params
     */
    public function distributeVideo($params)
    {
        $url = Config::DouYin_HOST . '/api/douyin/v1/video/upload_video_part/?open_id=' . $params['openId'];
        $body = [
            'video' => $params['video']
        ];
        $header = [
            'access-token' => $params['accessToken']
        ];
        $res = Client::post($url, $body, $header);
//        var_dump('fragment_id:' . $params['chunk_number']);
//        var_dump(json_decode($res->body, true));
//        return json_decode($res->body, true);
    }

    /**
     * 完成分片上传
     * @param $params
     * @return mixed
     */
    public function completeDistribute($params)
    {
        $url = Config::DouYin_HOST . '/api/douyin/v1/video/complete_video_part_upload/?open_id=' . $params['openId'] . '&upload_id=' . $params['upload_id'];
        $body = [];
        $header = [
            'access-token' => $params['accessToken']
        ];
        $res = Client::post($url, $body, $header);
//        var_dump('fragment_id:' . $params['chunk_number']);
//        var_dump(json_decode($res->body, true));
        return json_decode($res->body, true);
    }

    /**
     *创建视频抖音
     * @return mixed|string
     */
    public function createVideo($params)
    {
        if (!isset($params['accessToken']) || empty($params['accessToken'])) {
            return 'accessToken不能为空';
        }
        if (!isset($params['openId']) || empty($params['openId'])) {
            return 'openId不能为空';
        }
        if (!isset($params['video_id']) || empty($params['video_id'])) {
            return 'video_id不能为空';
        }
        $url = Config::DouYin_HOST . '/api/douyin/v1/video/create_video/?open_id=' . $params['openId'];
        $body = [
            'video_id' => $params['video_id'],
            'text' => $params['text'] ?? '',
        ];
        $header = [
            'access-token' => $params['accessToken']
        ];
        $res = Client::POST($url, $header, $body);
        return json_decode($res->body, true);
    }
}