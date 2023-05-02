<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');

$api_name = '蓝奏云解析';
$api_profile='在线解析蓝奏云下载链接，获取文件直链';
$version='1.0';
$author='molanp';
$type='一些工具';
$api_address="
|请求方式|请求地址|说明|
|---|---|---|
|get|[/api/lanzou](/api/lanzou)|-|";
$request_par='
|参数|说明|
|---|---|
|*`url`|蓝奏云文件分享链接|
|`pwd`|文件的下载密码(无密码请留空)|
|`down`|是否直接跳转下载，是则填`true`|';
$return_par='
|基础数据|说明|
|---|---|
|`name`|文件名称|
|`filesize`|文件大小|
|`url`|文件下载地址|';
if (handle_check()) {
    include_once('lanzou.php');
    $url = $_GET['url'] ?? NULL;
    $pwd = $_GET['pwd'] ?? NULL;
    $down = $_GET['down'] ?? NULL;
    $result = lanzou($url,$pwd);
    if ($down == true && $result["code"] == 200) {
        _return_($result["msg"]["url"],200,true);
    }
    _return_($result["msg"],$result["code"]);
}
?>