<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');

$api_name = '微博热搜';
$api_profile='一键获取微博热搜';
$version='1.0';
$author='molanp';
$type='一些工具';
$api_address=re_add(['GET'],['/api/wbtop'],['-']);
$request_par=re_par(['None'],['无需参数']);
$return_par=re_par(['rank','hot_word_num','category','hot_word','url'],['热搜排行','热度','所属分类','热搜','话题链接']);
if (handle_check()) {
    $wbtop = curl_get("https://weibo.com/ajax/side/hotSearch")['data']['realtime'];
    $i = 0;
    for($i;$i<count($wbtop);$i++) {
        if(!in_array('is_ad',$wbtop[$i])) {
            $top[$i] = [
                "rank"=>$i+1,
                "hot_word_num"=>$wbtop[$i]["num"],
                "category"=>$wbtop[$i]["category"],
                "hot_word"=>$wbtop[$i]["note"],
                "url"=>"https://s.weibo.com/weibo?q=%23{$wbtop[$i]["word"]}%23"];
        }
    };
    _return_($top);
}
?>