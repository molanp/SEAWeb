<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');

$api_name = '蓝奏云解析';
$api_profile='在线解析蓝奏云下载链接，获取文件直链';
$version='1.0';
$author='molanp';
$type='一些工具';
$api_address=re_add(['GET'],['/api/lanzou'],['-']);
$request_par=re_par(['*url','pwd','down'],['蓝奏云文件分享链接','文件的下载密码(无密码请留空)','是否直接跳转下载，是则填`true`']);
$return_par=re_par(['name','filesize','url'],['文件名称','文件大小','文件下载地址']);
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