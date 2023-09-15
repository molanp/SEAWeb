<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/path.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/connect.php');

$plugin_path = addSlashIfNeeded(preg_replace('/api\//',"",explode('?', $_SERVER['REQUEST_URI'])[0]));
include_once($_SERVER['DOCUMENT_ROOT'].'/services/connect.php');
$sql = "SELECT name, class, method, url_path, file_path FROM api WHERE url_path = :urlPath";
$statement = $database->prepare($sql);
$statement->execute([":urlPath" => $plugin_path]);
$data = $statement->fetch(PDO::FETCH_ASSOC);
if($data==null) {
    die(include_once($_SERVER['DOCUMENT_ROOT'].'/404.php'));
} elseif (handle_check($data["name"]) && $_SERVER['REQUEST_METHOD'] == $data["method"] || $data["method"] == "ALL"){
    $time = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? "Unknown";
    $url = "/api".$plugin_path ?? "Unknown";
    $referer = $_SERVER['HTTP_REFERER'] ?? "Unknown";
    $param = json($_REQUEST);
    $stmt = $database->prepare("INSERT INTO access_log (time, ip, url, referer, param) VALUES (?, ?, ?, ?, ?)");
    $stmt->bindParam(1, $time);
    $stmt->bindParam(2, $ip);
    $stmt->bindParam(3, $url);
    $stmt->bindParam(4, $referer);
    $stmt->bindParam(5, $param);
    $stmt->execute();
    
    include_once($data["file_path"]);
    $plugin = new $data["class"];
    $plugin->run(${'_'.$data["method"]});
} else {
    _return_("Method Not Allowed.Needed {$data["method"]}, but {$_SERVER['REQUEST_METHOD']} is given",405);
}
unset($plugin);
