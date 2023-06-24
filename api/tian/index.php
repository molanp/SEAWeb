<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');

$api_name = '舔狗日记';
$api_profile='一键获取舔狗日记';
$version='1.0';
$author='molanp';
$type='潮';
$api_address=re_add(['GET'],['/api/tian'],['-']);
$request_par=re_par(['None'],['无需参数']);
$return_par=re_par(['data'],['舔狗语录']);
if (handle_check()) {
    include_once('tgrj.php');
    _return_($data[array_rand($data)]);
}
?>