<?php

namespace VideoCompositing\Application;

use VideoCompositing\Config;
use VideoCompositing\Http\Client;

class KuaiShou
{
    public function getCode($params)
    {
        if (!isset($params['appId']) || empty($params['appId'])) {
            return 'appId不能为空';
        }
        if (!isset($params['loginType']) || empty($params['loginType'])) {
            return 'loginType不能为空';
        }
        if (!isset($params['redirectUri']) || empty($params['redirectUri'])) {
            return 'redirectUri不能为空';
        }
        switch ($params['loginType']) {
            case 'url':
                $url = Config::KuaiShow_HOST . '/oauth2/authorize?app_id=' . $params['appId'] . '&scope=user_info,user_video_publish,user_video_info&response_type=code&ua=pc&redirect_uri=' . $params['redirectUri'];
                break;
            case 'dynamicQR':
                $url = Config::KuaiShow_HOST . '/oauth2/connect?app_id=' . $params['appId'] . '&scope=user_info,user_video_publish,user_video_info&response_type=code&ua=pc&redirect_uri=' . $params['redirectUri'];
                break;
            case 'staticQR':
                $url = Config::KuaiShow_HOST . '/oauth2/qr_code?app_id=' . $params['appId'] . '&scope=user_info,user_video_publish,user_video_info&response_type=code&ua=pc&redirect_uri=' . $params['redirectUri'];
                break;
            default:
                return '参数错误！';
        }
        header("Location: $url");
        exit();
    }

    public function getAccessToken($params)
    {
        if (!isset($params['appId']) || empty($params['appId'])) {
            return 'appId不能为空';
        }
        if (!isset($params['appSecret']) || empty($params['appSecret'])) {
            return 'appSecret不能为空';
        }
        if (!isset($params['code']) || empty($params['code'])) {
            return 'code不能为空';
        }
        $url = Config::KuaiShow_HOST . '/oauth2/access_token?app_id=' . $params['appId'] . '&app_secret=' . $params['appSecret'] . '&code=' . $params['code'] . '&grant_type=authorization_code';
        $res = Client::get($url);
        return json_decode($res->body, true);
    }

    public function refreshAccessToken()
    {
        if (!isset($params['appId']) || empty($params['appId'])) {
            return 'appId不能为空';
        }
        if (!isset($params['appSecret']) || empty($params['appSecret'])) {
            return 'appSecret不能为空';
        }
        if (!isset($params['refresh_token']) || empty($params['refresh_token'])) {
            return 'refresh_token不能为空';
        }
        $url = Config::KuaiShow_HOST . '/oauth2/refresh_token?app_id=' . $params['appId'] . '&app_secret=' . $params['appSecret'] . '&refresh_token=' . $params['refresh_token'] . '&grant_type=refresh_token';
        $res = Client::get($url);
        return json_decode($res->body, true);
    }

    public function getUploadToken($params)
    {
        if (!isset($params['appId']) || empty($params['appId'])) {
            return 'appId不能为空';
        }
        if (!isset($params['accessToken']) || empty($params['accessToken'])) {
            return 'accessToken不能为空';
        }
        $url = Config::KuaiShow_HOST . '/openapi/photo/start_upload?access_token=' . $params['accessToken'] . '&app_id=' . $params['appId'];
        $res = Client::post($url, []);
        return json_decode($res->body, true);
    }

    public function uploadVideo($params)
    {
        if (!isset($params['uploadToken']) || empty($params['uploadToken'])) {
            return 'uploadToken不能为空';
        }
        if (!isset($params['endpoint']) || empty($params['endpoint'])) {
            return 'endpoint不能为空';
        }
        if (!isset($params['file']) || empty($params['file'])) {
            return 'file不能为空';
        }
        $maxFileSize = 1048576 * 5;
//        $fileSize=$params['file']->getSize();
        $fileSize=$this->getVideoSize($params['file']);

        $save_to = dirname(dirname(__DIR__)) . '/1.mp4';
        $content = file_get_contents($params['file']);
        file_put_contents($save_to, $content);
        $params['file']=$save_to;

        if ($fileSize < $maxFileSize) {//文件小于5M直接上传文件
            $url = 'http://' . $params['endpoint'] . '/api/upload/multipart?upload_token=' . $params['uploadToken'];
            $body = [
                'file' => $params['file']
            ];
            $res = Client::post($url, $body);
            return json_decode($res->body, true);
        } else {//否则分片上传
            $file_handle = fopen($params['file'], 'rb');
            $chunk_number = 0;
            while (!feof($file_handle)) {
                // 读取指定大小的数据
                $chunk_data = fread($file_handle, $maxFileSize);
                $params['chunk_number'] = $chunk_number;
                $params['file'] = $chunk_data;
                $this->distributeVideo($params);
                // 增加分片编号
                $chunk_number++;
            }
            fclose($file_handle);
            $params['chunk_number'] = $chunk_number;
            return $this->completeDistribute($params);
        }
    }

    public function distributeVideo($params)
    {
        $url = 'http://' . $params['endpoint'] . '/api/upload/fragment?fragment_id=' . $params['chunk_number'] . '&upload_token=' . $params['uploadToken'];
        $body = $params['file'];
        $header = [
            'Content-Type' => 'video/mp4'
        ];
        $res = Client::post($url, $body, $header);
        return json_decode($res->body, true);
    }

    public function completeDistribute($params)
    {
        $url = 'http://' . $params['endpoint'] . '/api/upload/complete?upload_token=' . $params['uploadToken'] . '&fragment_count=' . $params['chunk_number'];
        $res = Client::post($url, []);
        return json_decode($res->body, true);
    }

    public function uploadVideo2($params)
    {
        $file_handle = fopen($params['file'], 'rb');
        fclose($file_handle);
        $url = 'http://' . $params['endpoint'] . '/api/upload?upload_token=' . $params['uploadToken'];
        $body = $file_handle;
        $header = [
            'Content-Type' => 'video/mp4'
        ];
        $res = Client::post($url, $body, $header);
        return json_decode($res->body, true);
    }

    public function resumeVideo($params)
    {
        if (!isset($params['uploadToken']) || empty($params['uploadToken'])) {
            return 'uploadToken不能为空';
        }
        if (!isset($params['endpoint']) || empty($params['endpoint'])) {
            return 'endpoint不能为空';
        }
        $url = 'http://' . $params['endpoint'] . '/api/upload/resume?upload_token=' . $params['uploadToken'];
        $res = Client::get($url);
        return json_decode($res->body, true);
    }

    public function releaseVideo($params)
    {
        if (!isset($params['appId']) || empty($params['appId'])) {
            return 'appId不能为空';
        }
        if (!isset($params['accessToken']) || empty($params['accessToken'])) {
            return 'accessToken不能为空';
        }
        if (!isset($params['uploadToken']) || empty($params['uploadToken'])) {
            return 'uploadToken不能为空';
        }
        if (!isset($params['cover']) || empty($params['cover'])) {
            return 'cover不能为空';
        }
        if (!isset($params['caption']) || empty($params['caption'])) {
            return 'caption不能为空';
        }

        $url = Config::KuaiShow_HOST . '/openapi/photo/publish?access_token=' . $params['accessToken'] . '&app_id=' . $params['appId'] . '&upload_token=' . $params['uploadToken'];
        $body = [
            'cover' => $params['cover'],//封面图
            'caption' => $params['caption']//标题
        ];
        $res = Client::post($url, $body);
        return json_decode($res->body, true);
    }

    public function getVideo($params)
    {
        if (!isset($params['appId']) || empty($params['appId'])) {
            return 'appId不能为空';
        }
        if (!isset($params['accessToken']) || empty($params['accessToken'])) {
            return 'accessToken不能为空';
        }
        $url = Config::KuaiShow_HOST . '/openapi/photo/list?access_token=' . $params['accessToken'] . '&app_id=' . $params['appId'];
        $res = Client::get($url);
        return json_decode($res->body, true);
    }

    public function getVideoSize($videoUrl) {
        $headers = get_headers($videoUrl, true);

        if (strpos($headers[0], '200') === false) {
            return false; // Request failed or video not found
        }
        $contentLength = isset($headers['Content-Length']) ? intval($headers['Content-Length']) : 0;
        return $contentLength;
    }



}