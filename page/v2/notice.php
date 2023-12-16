<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/services/Config.class.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/services/until.php");

logger();
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $WEB= new Data();
    $data = $WEB->get("web");
    $time = $WEB->time("web");
    _return_(["notice"=>$data["notice"],"time"=>$time["notice"]]);
}