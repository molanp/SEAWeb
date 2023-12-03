<?php
class video_spider
{
    public function getInfo()
    {
        return [
            'name' => '聚合短视频去水印',
            'version' => '1.0',
            'profile' => '目前支持以下平台视频去水印下载<br>
            - pipix（皮皮虾）
            - douyin（抖音）
            - huoshan（火山短视频）
            - weibo.com（微博）
            - oasis.weibo（微博）
            - zuiyou（最右）
            - xiaochuankeji（小咖秀）
            - bbq.bilibili（B站）
            - kuaishou（快手短视频）
            - quanmin（全民小视频）
            - hanyuhl（Before避风）
            - eyepetizer（开眼视频）
            - immomo（陌陌）
            - vuevideo（vuevlog）
            - xiaokaxiu（小咖秀）
            - ippzone 或者 pipigx（皮皮搞笑）
            - qq.com（全民K歌）
            - ixigua.com（西瓜视频）
            - doupai（逗拍）
            - 6.cn（6间房）
            - huya.com/play/（虎牙）
            - pearvideo.com（梨视频）
            - xinpianchang.com（新片场）
            - acfun.cn（Acfun）
            - meipai.com（美拍）',
            'method' => 'POST',
            'author' => '5ime',
            'request_par' => re_par(['*url' => "需要解析的视频URL"]),
            'return_par' => re_par([
                'author' => '视频作者',
                'avatar' => '作者头像',
                'like' => '视频点赞量',
                'time' => '视频发布时间',
                'title' => '视频标题',
                'cover' => '视频封面',
                'url' => '视频无水印链接',
                'sex' => '作者性别',
                'age' => '作者年龄',
                'city' => '所在城市',
                'uid' => '作者id'
            ])
        ];
    }
    public function run($request)
    {
        $url = $request['url'] ?? '';
        include_once(__DIR__."/func.php");
        $api = new Video();
        if (strpos($url, 'pipix')) {
            $arr = $api->pipixia($url);
        } elseif (strpos($url, 'douyin')) {
            $arr = $api->douyin($url);
        } elseif (strpos($url, 'huoshan')) {
            $arr = $api->huoshan($url);
        } elseif (strpos($url, 'weibo.com')) {
            $arr = $api->weibo($url);
        } elseif (strpos($url, 'oasis.weibo')) {
            $arr = $api->lvzhou($url);
        } elseif (strpos($url, 'zuiyou')) {
            $arr = $api->zuiyou($url);
        } elseif (strpos($url, 'xiaochuankeji')) {
            $arr = $api->zuiyou($url);
        } elseif (strpos($url, 'bbq.bilibili')) {
            $arr = $api->bbq($url);
        } elseif (strpos($url, 'kuaishou')) {
            $arr = $api->kuaishou($url);
        } elseif (strpos($url, 'quanmin')) {
            $arr = $api->quanmin($url);
        } elseif (strpos($url, 'hanyuhl')) {
            $arr = $api->before($url);
        } elseif (strpos($url, 'eyepetizer')) {
            $arr = $api->kaiyan($url);
        } elseif (strpos($url, 'immomo')) {
            $arr = $api->momo($url);
        } elseif (strpos($url, 'vuevideo')) {
            $arr = $api->vuevlog($url);
        } elseif (strpos($url, 'xiaokaxiu')) {
            $arr = $api->xiaokaxiu($url);
        } elseif (strpos($url, 'ippzone') || strpos($url, 'pipigx')) {
            $arr = $api->pipigaoxiao($url);
        } elseif (strpos($url, 'qq.com')) {
            $arr = $api->quanminkge($url);
        } elseif (strpos($url, 'ixigua.com')) {
            $arr = $api->xigua($url);
        } elseif (strpos($url, 'doupai')) {
            $arr = $api->doupai($url);
        } elseif (strpos($url, '6.cn')) {
            $arr = $api->sixroom($url);
        } elseif (strpos($url, 'huya.com/play/')) {
            $arr = $api->huya($url);
        } elseif (strpos($url, 'pearvideo.com')) {
            $arr = $api->pear($url);
        } elseif (strpos($url, 'xinpianchang.com')) {
            $arr = $api->xinpianchang($url);
        } elseif (strpos($url, 'acfun.cn')) {
            $arr = $api->acfan($url);
        } elseif (strpos($url, 'meipai.com')) {
            $arr = $api->meipai($url);
        } else {
            $arr = [
                "code" => 201,
                "data" => '不支持您输入的链接'
            ];
        }
        if (!empty($arr)) {
            $status = $arr["code"];
            $arr = $arr["data"];
        } else {
            $arr = '解析失败';
            $status = 201;
        }
        _return_($arr, $status);
    }
}
