<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/path.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/connect.php');

$plugin_path = preg_replace('/api\//',"",$_SERVER['REQUEST_URI']);
include_once($_SERVER['DOCUMENT_ROOT'].'/services/connect.php');
$sql = "SELECT class, file_path FROM api WHERE url_path = :urlPath";
$statement = $database->prepare($sql);
$statement->execute([":urlPath" => $plugin_path]);
$data = $statement->fetch(PDO::FETCH_ASSOC);
if($data==null) {
    die(include_once($_SERVER['DOCUMENT_ROOT'].'/404.php'));
} else {
    include_once($data["file_path"]);
    $plugin = new $data["class"];
    $plugin->run($_REQUEST);
}