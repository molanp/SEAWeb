<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/Config.class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');

$DATA = new Config($_SERVER['DOCUMENT_ROOT'].'/db/status');
$filename = scandir('./');
foreach($filename as $v){
    if($v=="." || $v==".."){continue;}
    if(!strpos($v,'.php')) {
        include_once("$v/index.php");
        if(!isset($type) or $type == 'None') {$type='一些工具';}
        if($DATA->get('status')){$status=$DATA->get('status')[$api_name];} else {$status='true';}
        $conname[$type][$api_name] = [
            'path'=>$v,
            'api_profile'=>trim($api_profile),
            'api_address'=>$api_address,
            'version'=>$version,
            'author'=>$author,
            'request_parameters'=>trim($request_par),
            'return_parameters'=>trim($return_par),
            'status'=>$status
        ];
    }
    $api_name = $api_profile = $api_address = $version = $author = $request_parameters = $return_parameters = $type = $status = 'None';
}
die(json_encode($conname,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
?>