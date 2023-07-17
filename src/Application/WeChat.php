<?php

namespace VideoCompositing\Application;

use VideoCompositing\Config;
use VideoCompositing\Http\Client;

class WeChat
{
    /**
     * 获取code
     * @param $params
     * @return string
     */
    public function getAccessToken($params)
    {
        if (!isset($params['appid']) || empty($params['appid'])) {
            return 'appid不能为空';
        }
        if (!isset($params['secret']) || empty($params['secret'])) {
            return 'secret不能为空';
        }
        $url = Config::WeChat_HOST . '/cgi-bin/token?grant_type=client_credential&appid=' . $params['appid'] . '&secret=' . $params['secret'];
        $res = Client::get($url);
        return json_decode($res->body, true);
    }

    /**
     * 上传封面图片
     */
    public function uploadMedia($params)
    {
        if (!isset($params['access_token']) || empty($params['access_token'])) {
            return 'access_token不能为空';
        }
        if (!isset($params['media']) || empty($params['media'])) {
            return 'media不能为空';
        }
        $url = Config::WeChat_HOST . '/cgi-bin/material/add_material?access_token=' . $params['access_token'] . '&type=image';

        $save_to = dirname(dirname(__DIR__)) . '/1.jpg';
        $content = file_get_contents($params['media']);
        file_put_contents($save_to, $content);
        $data = array(
            'media' => new \CURLFile($save_to)
        );
        $headers['Content-Type'] = 'multipart/form-data';
        $res = Client::POST($url, $data, $headers);
        return json_decode($res->body, true);
    }

    /**
     * 创建草稿
     * @param $params
     */
    public function createDraft($params)
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
        if (!isset($params['thumb_media_id']) || empty($params['thumb_media_id'])) {
            return 'thumb_media_id不能为空';
        }
        $url = Config::WeChat_HOST . '/cgi-bin/draft/add?access_token=' . $params['access_token'];
        $articles = [
            [
                'title' => $params['title'],
                'author' => $params['author'] ?? '',//作者
                'digest' => $params['digest'] ?? '',//图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空。如果本字段为没有填写，则默认抓取正文前54个字
                'content' => $params['content'],//内容
                'content_source_url' => $params['content_source_url'],//图文消息的原文地址，即点击“阅读原文”后的URL
                'thumb_media_id' => $params['thumb_media_id'],
                'need_open_comment' => $params['need_open_comment'] ?? 1,//Uint32 是否打开评论，0不打开(默认)，1打开
                'only_fans_can_comment' => $params['only_fans_can_comment'] ?? 0//Uint32 是否粉丝才可评论，0所有人可评论(默认)，1粉丝才可评论
            ]
        ];
        $body = [
            'articles' => $articles
        ];
        $res = Client::POST($url, json_encode($body, JSON_UNESCAPED_UNICODE));
        return json_decode($res->body, true);
    }

    public function publish($params)
    {
        if (!isset($params['access_token']) || empty($params['access_token'])) {
            return 'access_token不能为空';
        }
        if (!isset($params['media_id']) || empty($params['media_id'])) {
            return 'media_id不能为空';
        }
        $url = Config::WeChat_HOST . '/cgi-bin/freepublish/submit?access_token=' . $params['access_token'];
        $body = [
            'media_id' => $params['media_id']
        ];
        $res = Client::POST($url, json_encode($body, JSON_UNESCAPED_UNICODE));
        return json_decode($res->body, true);
    }
}