<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/Config.class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $WEB= new Config($_SERVER['DOCUMENT_ROOT'].'/data/web');
    $WEB = $WEB->get('web');
    _return_(["notice"=>$WEB["notice"]["data"],"time"=>$WEB["notice"]["latesttime"]]);
}