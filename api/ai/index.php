<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');

$api_name = 'AI回复';
$api_profile='搭载青云客ai与本地词库，相对智能的ai.与ai普普通通的对话吧！

_~~或许词库有点二次元?~~_';
$version='1.0';
$author='molanp';
$type='一些工具';
$api_address="
|请求方式|请求地址|说明|
|---|---|---
|get|[/api/ai](/api/ai)|-|";
$request_par='
|参数|说明|
|---|---|
|*`msg`|你要对ai说的话|';
$return_par='
|基础数据|说明|
|---|---|
|`data`|ai给你的回复|';

function findMostSimilarWord($input, $dictionary) {
    $bestMatch = '';
    $bestScore = 0;
    foreach ($dictionary as $word) {
        $score = similar_text($input, $word);
        if ($score > $bestScore) {
            $bestScore = $score;
            $bestMatch = $word;
        }
    }
    if ($bestScore >= 0.5) {
        return $bestMatch;
    } else {
        return NULL;
    }
}

if ($_SERVER['REQUEST_URI'] != '/api/') {
    handle_check();
    include_once('anime.php');
    $wozai = [
        "哦豁？！",
        "你好！Ov<",
        "库库库，呼唤咱做什么呢",
        "我在呢！",
        "呼呼，叫俺干嘛",
    ];
    $budong = [
        "你在说啥子？",
        "纯洁的咱没听懂",
        "下次再告诉你(下次一定)",
        "你觉得我听懂了吗？嗯？",
        "我！不！知！道！",
    ];
    if (isset($_GET['msg'])) {
        $msg = $_GET['msg'];
    } else {
        $msg= NULL;
    }
    if (!empty($msg)) {
        foreach(["你好啊","你好","在吗","在不在","您好","您好啊","你好","在",] as $zai){
            if (strpos($msg,$zai)!==false) {
                $data = $wozai[array_rand($wozai)];
                break;
            } else {$data = NULL;}
        }
        if (empty($data)) {
            $words = $anime[findMostSimilarWord($msg,array_keys($anime))];
            $data = $words[array_rand($words)];
        }
        if (empty($data)) {
            $result = curl_get("http://api.qingyunke.com/api.php",["key"=>"free","appid"=>0,"msg"=>$msg])["data"];
            if ($result["result"]==0) {
                $data = $result["content"];
                str_ireplace("菲菲","咱",$data);
                str_ireplace("艳儿","咱",$data);
                str_ireplace("{br}","\n",$data);
                if(strpos($data,"提示")!==false){$data=mb_substr($data,0,strpos($data,"提示"),"utf-8");}
                if(strpos($data,"公众号")!==false){$data=NULL;}
                if(strpos($data,"taobao.com")!==false){$data=NULL;}
                if(strpos($data,"淘宝")!==false){$data=NULL;}
                $data = preg_replace('/{face:(\d+)}/','', $data);
            }
        }
        if (empty($data)) {
            $data = $budong[array_rand($budong)];
        }
        _return_($data, 200);
    } else {
        _return_("请求内容有误", 400);
    }
}
?>