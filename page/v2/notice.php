<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/services/Config.class.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/services/until.php");

req_log();
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $WEB= new Data();
    $data = $WEB->get("web",true);
    _return_(["notice"=>$data["notice"][0],"time"=>$data["notice"][1]]);
}