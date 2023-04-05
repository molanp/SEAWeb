<?php
//curl_get获取数据
function curl_get($url,$data=[]){
    if($url == "" ){
        return false;
    }
    $url = $url.'?'.http_build_query($data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true) ;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER,0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $output = curl_exec($ch);
    if(curl_exec($ch) === false){
        echo 'Curl error: ' . curl_error($ch);
    }
    curl_close($ch);
    return json_decode($output,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
}
//return
function _return_($context,$status=200,$location=false) {
    header('Access-Control-Allow-Origin: *'); // 允许跨域请求
    header('Access-Control-Allow-Methods: POST,GET,OPTIONS,DELETE,PUT'); // 允许全部请求类型
    header('Access-Control-Allow-Credentials: true'); // 允许发送 cookies
    header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 允许自定义请求头的字段
    if ($location == false) {
        header('Content-type:text/json;charset=utf-8');
        die(json_encode(['status'=>$status,'data'=>$context,'time'=>time()],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
    } else {
        die(header("Location: $context"));
    }
}
?>