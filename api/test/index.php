<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');

$host = 'http://'.$_SERVER['HTTP_HOST'];
$api_name = '测试插件';
$api_profile='只是一个测试插件';
$version='1.0';
$author='molanp';
$type='野兽先辈';
$api_address="
|请求方式|请求地址|说明|
|---|---|---
|get|$host/api/test|哼哼哼|";
$request_par='
|参数|说明|
|---|---|
|*`homo`|一个随机恶臭数字|';
$return_par='
|基础数据|说明|
|---|---|
|`level`|恶臭的等级|';
if ($_SERVER['REQUEST_URI'] != '/api/') {
    if (isset($_GET['homo'])) {
        $homo = $_GET['homo'];
    } else {
        $homo= NULL;
    }
    if (!empty($homo)) {
        $data = ["level"=>$homo*114514];
        _return_($data, 200);
    } else {
        _return_("请求格式有误", 500);
    }
}
?>