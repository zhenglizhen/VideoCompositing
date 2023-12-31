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
        $url = Config::DouYin_HOST . '/platform/oauth/connect/?client_key=' . $params['clientKey'] . '&response_type=code&scope=user_info,video.create.bind,video.data.bind,video.list.bind,data.external.item,data.external.user,fans.data.bind,data.external.fans_source,data.external.fans_favourite&redirect_uri=' . $params['redirectUri'] . '&state=STATE';
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
        $url = Config::DouYin_HOST . '/oauth/access_token/';
        $body = [
            'client_secret' => $params['clientSecret'],
            'code' => $params['code'],
            'grant_type' => 'authorization_code',
            'client_key' => $params['clientKey'],
        ];
        $res = Client::POST($url, $body);
        return json_decode($res->body, true);
    }

    public function getUserInfo($params)
    {
        if (!isset($params['openId']) || empty($params['openId'])) {
            return 'openId不能为空';
        }
        if (!isset($params['accessToken']) || empty($params['accessToken'])) {
            return 'accessToken不能为空';
        }
        $url = Config::DouYin_HOST . '/oauth/userinfo/';
        $body=[
            'access_token'=>$params['accessToken'],
            'open_id'=>$params['openId'],
        ];
        $res = Client::POST($url,$body);
        return json_decode($res->body, true);
    }

    public function refreshAccessToken($params)
    {
        if (!isset($params['client_key']) || empty($params['client_key'])) {
            return 'client_key不能为空';
        }
        if (!isset($params['refresh_token']) || empty($params['refresh_token'])) {
            return 'refresh_token不能为空';
        }
        $url = Config::DouYin_HOST . '/oauth/refresh_token/';
        $body = [
            'client_key' => $params['client_key'],
            'grant_type' => 'refresh_token',
            'refresh_token' => $params['refresh_token'],
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
            'image' => new \CURLFile($params['image'])
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
        $res = Client::POST($url, json_encode($body, JSON_UNESCAPED_UNICODE), $header);
        return json_decode($res->body, true);
    }

    /**
     * 直接上传视频
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
        if (!isset($params['fileSize']) || empty($params['fileSize'])) {
            return 'fileSize不能为空';
        }
        $maxFileSize = 1048576 * 10;
        if ($params['fileSize'] < $maxFileSize) {//文件小于10M直接上传文件

            $url = Config::DouYin_HOST . '/api/douyin/v1/video/upload_video/?open_id=' . $params['openId'];
            $body = [
                'video' => new \CURLFile($params['video'])
            ];
            $header = [
                'content-type' => 'multipart/form-data',
                'access-token' => $params['accessToken']
            ];
            $res = Client::post($url, $body, $header);
            return json_decode($res->body, true);
        } else {//分片上传
            return "请使用分片上传";
        }
    }

    /**
     * 分片上传初始化
     * @param $params
     */
    public function initDistribute($params)
    {
        if (!isset($params['accessToken']) || empty($params['accessToken'])) {
            return 'accessToken不能为空';
        }
        if (!isset($params['openId']) || empty($params['openId'])) {
            return 'openId不能为空';
        }
        $url = Config::DouYin_HOST . '/api/douyin/v1/video/init_video_part_upload/?open_id=' . $params['openId'];
        $header = [
            'access-token' => $params['accessToken']
        ];
        $res = Client::post($url, [], $header);
        $res = (json_decode($res->body, true));
        return $res['data']['upload_id'];
    }

    /**
     * 分片上传
     * @param $params
     */
    public function distributeVideo($params)
    {
        if (!isset($params['accessToken']) || empty($params['accessToken'])) {
            return 'accessToken不能为空';
        }
        if (!isset($params['video']) || empty($params['video'])) {
            return 'video不能为空';
        }
        if (!isset($params['part_number']) || empty($params['part_number'])) {
            return 'part_number不能为空';
        }
        if (!isset($params['upload_id']) || empty($params['upload_id'])) {
            return 'upload_id不能为空';
        }
        if (!isset($params['openId']) || empty($params['openId'])) {
            return 'openId不能为空';
        }


        $url = Config::DouYin_HOST . '/api/douyin/v1/video/upload_video_part/?open_id=' . $params['openId'] . '&upload_id=' . $params['upload_id'] . '&part_number=' . $params['part_number'];
        $body = [
            'video' => new \CURLFile($params['video'])
        ];
        $header = [
            'content-type' => 'multipart/form-data',
            'access-token' => $params['accessToken']
        ];
        $res = Client::post($url, $body, $header);
    }

    /**
     * 完成分片上传
     * @param $params
     * @return mixed
     */
    public function completeDistribute($params)
    {
        if (!isset($params['accessToken']) || empty($params['accessToken'])) {
            return 'accessToken不能为空';
        }
        if (!isset($params['openId']) || empty($params['openId'])) {
            return 'video不能为空';
        }
        if (!isset($params['upload_id']) || empty($params['upload_id'])) {
            return 'part_number不能为空';
        }
        $url = Config::DouYin_HOST . '/api/douyin/v1/video/complete_video_part_upload/?open_id=' . $params['openId'] . '&upload_id=' . $params['upload_id'];
        $body = [];
        $header = [
            'access-token' => $params['accessToken']
        ];
        $res = Client::post($url, $body, $header);
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
        $res = Client::POST($url, json_encode($body, JSON_UNESCAPED_UNICODE), $header);
        return json_decode($res->body, true);
    }

    public function getVideo($params)
    {

        if (!isset($params['openId']) || empty($params['openId'])) {
            return 'openId不能为空';
        }
        if (!isset($params['accessToken']) || empty($params['accessToken'])) {
            return 'accessToken不能为空';
        }

        $params['count']=$params['count']??10;
        $params['cursor']=$params['cursor']??0;

        $url = Config::DouYin_HOST . '/api/douyin/v1/video/video_list/?open_id=' . $params['openId'].'&cursor='.$params['cursor'].'&count='.$params['count'];

        $header = [
            'access-token' => $params['accessToken']
        ];
        $res = Client::get($url, $header);
        return json_decode($res->body, true);
    }

    public function getVideoData($params)
    {
        if (!isset($params['accessToken']) || empty($params['accessToken'])) {
            return 'accessToken不能为空';
        }
        if (!isset($params['openId']) || empty($params['openId'])) {
            return 'openId不能为空';
        }
        if (!isset($params['item_ids']) || empty($params['item_ids'])) {
            return 'item_ids不能为空';
        }

        $url = Config::DouYin_HOST . '/api/douyin/v1/video/video_data/?open_id=' . $params['openId'];
        $header = [
            'access-token' => $params['accessToken']
        ];
        $body = [
            'item_ids' => $params['item_ids']
        ];

        $res = Client::POST($url, json_encode($body, JSON_UNESCAPED_UNICODE), $header);
        return json_decode($res->body, true);
    }


    public function getVideoSize($videoUrl)
    {
        $headers = get_headers($videoUrl, true);

        if (strpos($headers[0], '200') === false) {
            return false; // Request failed or video not found
        }
        $contentLength = isset($headers['Content-Length']) ? intval($headers['Content-Length']) : 0;
        return $contentLength;
    }
    //用户数据
    public function getUserBaseData($params)
    {
        if (!isset($params['openId']) || empty($params['openId'])) {
            return 'openId不能为空';
        }

        $url = '/data/external/user/item/';
        switch ($params['url']) {
            case 'item':
                $url = '/data/external/user/item/';
                break;
            case 'fans':
                $url = '/data/external/user/fans/';
                break;
            case 'like':
                $url = '/data/external/user/like/';
                break;
            case 'comment':
                $url = '/data/external/user/comment/';
                break;
            case 'share':
                $url = '/data/external/user/share/';
                break;
            case 'profile':
                $url = '/data/external/user/profile/';
                break;
        }
        $url = Config::DouYin_HOST . $url . '?open_id=' . $params['openId'];
        if(isset($params['date_type'])){
            $url .='&date_type='.$params['date_type'];
        }else{
            $url .='&date_type=7';
        }
        $header = [
            'access-token' => $params['accessToken']
        ];
        $res = json_decode(Client::get($url, $header)->body, true);
        if($res['data']['error_code']){
            return $res['data'];
        }else{
            return $res['data']['result_list'];
        }
    }

    //粉丝数据
    public function getFansBaseData($params)
    {
        if (!isset($params['openId']) || empty($params['openId'])) {
            return 'openId不能为空';
        }

        $url = '/api/douyin/v1/user/fans_data/';
        switch ($params['url']) {
            case 'data':
                $url = '/api/douyin/v1/user/fans_data/';
                break;
            case 'source':
                $url = '/data/extern/fans/source/';
                break;
            case 'favourite':
                $url = '/data/extern/fans/favourite/';
                break;
            case 'comment':
                $url = '/data/extern/fans/comment/';
                break;
        }
        $url = Config::DouYin_HOST . $url . '?open_id=' . $params['openId'];
        $header = [
            'access-token' => $params['accessToken']
        ];
        $res = Client::get($url, $header);
        return json_decode($res->body, true);
    }

    //视频数据
    public function getBaseData($params)
    {
        if (!isset($params['openId']) || empty($params['openId'])) {
            return 'openId不能为空';
        }
        if (!isset($params['item_id']) || empty($params['item_id'])) {
            return 'item_id不能为空';
        }
        $url = '/data/external/item/base/';
        switch ($params['url']) {
            case 'base':
                $url = '/data/external/item/base/';
                break;
            case 'like':
                $url = '/data/external/item/like/';
                break;
            case 'comment':
                $url = '/data/external/item/comment/';
                break;
            case 'play':
                $url = '/data/external/item/play/';
                break;
            case 'share':
                $url = '/data/external/item/share/';
                break;
        }
        $params['item_id'] = urlencode($params['item_id']);
        $url = Config::DouYin_HOST . $url . '?open_id=' . $params['openId'] . '&item_id=' . $params['item_id'];
        if(isset($params['date_type'])){
            $url .='&date_type='.$params['date_type'];
        }
        $header = [
            'access-token' => $params['accessToken']
        ];
        $res = Client::get($url, $header);
        $res=json_decode($res->body, true);
        return $res;
    }
}