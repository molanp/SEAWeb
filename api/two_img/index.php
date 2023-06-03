<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');

$api_name = '二次元图片';
$api_profile='随机返回二次元图片';
$version='1.0';
$author='molanp';
$type='随机图片';
$api_address=re_add(['GET'],['/api/two_img'],['-']);
$request_par=re_par(['type'],['填`json`则返回图片json信息，不填则返回一张图片']);
$return_par=re_par(['data'],['图片url']);

if (handle_check()) {
    include_once('array.php');
    $pic = $pics[array_rand($pics)];
    $type = $_GET['type'] ?? NULL;
    switch($type){
        case 'json':
            _return_($pic);
        default:
            _return_($pic,200,true);
    }
}
?>