<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');

$api_name = '舔狗日记';
$api_profile='一键获取舔狗日记';
$version='1.0';
$author='molanp';
$type='一些工具';
$api_address="
|请求方式|请求地址|说明|
|---|---|---|
|get|[/api/tian](/api/tian)|-|";
$request_par='
|参数|说明|
|---|---|
|`None`|无需任何参数|';
$return_par='
|基础数据|说明|
|---|---|
|`data`|舔狗语录|';
if (handle_check()) {
    include_once('tgrj.php');
    _return_($data[array_rand($data)]);
}
?>