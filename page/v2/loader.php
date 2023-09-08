<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/path.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/connect.php');

$plugin_path = preg_replace('/api\//',"",$_SERVER['REQUEST_URI']);
include_once($_SERVER['DOCUMENT_ROOT'].'/services/connect.php');
$sql = "SELECT name, class, url_path, file_path FROM api WHERE url_path = :urlPath";
$statement = $database->prepare($sql);
$statement->execute([":urlPath" => $plugin_path]);
$data = $statement->fetch(PDO::FETCH_ASSOC);
if($data==null) {
    die(include_once($_SERVER['DOCUMENT_ROOT'].'/404.php'));
} elseif (handle_check($data["name"])){
    $time = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    $url = $_SERVER['REQUEST_URI'];
    $referer = $_SERVER['HTTP_REFERER'];
    $param = isset($_REQUEST['param']) ? $_REQUEST['param'] : '';
    $query = "INSERT INTO access_log (time, ip, url, referer, param) VALUES ('$time', '$ip', '$url', '$referer', '$param')";
    $database->exec($query);
    include_once($data["file_path"]);
    $plugin = new $data["class"];
    $plugin->run($_REQUEST);
}