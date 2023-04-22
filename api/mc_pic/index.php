<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');

$pics = ["https://pan.whgpc.com/view.php/27b7c41acda522aee52d08fc34eeafba.jpg",
    "https://pan.whgpc.com/view.php/5d0823e6174755772d7e73533521a2a1.jpg",
    "https://pan.whgpc.com/view.php/0e996ec05f02b62f880d87fb6c5abdde.jpg",
    "https://images8.alphacoders.com/413/413114.jpg",
    "https://images5.alphacoders.com/488/488041.png",
    "https://images.alphacoders.com/246/246223.jpg",
    "https://images5.alphacoders.com/285/285765.jpg",
    "https://images3.alphacoders.com/232/232835.jpg",
    "https://images7.alphacoders.com/373/373184.png",
    "https://images.alphacoders.com/547/547158.jpg",
    "https://images8.alphacoders.com/556/556717.jpg",
    "https://images3.alphacoders.com/557/557420.jpg",
    "https://images6.alphacoders.com/561/561493.jpg",
    "https://images7.alphacoders.com/730/730512.jpg",
    "https://images5.alphacoders.com/733/733612.png",
    "https://images.alphacoders.com/783/783153.png",
    "https://images7.alphacoders.com/811/811249.png",
    "https://images2.alphacoders.com/492/492821.png",
    "https://images6.alphacoders.com/423/423532.jpg",
    "https://images2.alphacoders.com/879/879339.jpg",
    "https://images4.alphacoders.com/101/101854.jpg",];

$api_name = 'MC 美图';
$api_profile='随机返回mc图片';
$version='1.0';
$author='molanp';
$type='随机图片';
$api_address="
|请求方式|请求地址|说明|
|---|---|---|
|get|[/api/mc_pic](/api/mc_pic)|-|";
$request_par='
|参数|说明|
|---|---|
|`type`|填`json`则返回图片json信息，不填则返回一张图片|';
$return_par='
|基础数据|说明|
|---|---|
|`/`|图片url|';

if ($_SERVER['REQUEST_URI'] != '/api/') {
    handle_check();
    $pic = $pics[array_rand($pics)];
    if (isset($_GET['type'])) {
        $type = $_GET['type'];
    } else {
        $type = NULL;
    }
    switch($type){
        case 'json':
            _return_($pic);
        default:
            _return_($pic,200,true);
    }
}
?>