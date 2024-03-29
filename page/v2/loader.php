<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/until.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/path.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/connect.php");

$url_path = $_REQUEST["__"];

$plugin_path = addSlashIfNeeded($url_path ?? "/");
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/connect.php");
$sql = "SELECT name, class, method, url_path, file_path FROM api WHERE url_path = :urlPath";
$statement = $DATABASE->prepare($sql);
$statement->execute([":urlPath" => $plugin_path]);
$data = $statement->fetch(PDO::FETCH_ASSOC);
if ($data == null) {
    die(include_once($_SERVER["DOCUMENT_ROOT"] . "/404.php"));
} elseif (handle_check($data["name"]) && $_SERVER["REQUEST_METHOD"] == $data["method"] || $data["method"] == "ALL" || in_array($_SERVER["REQUEST_METHOD"], explode('/', $data["method"]))) {
    include_once($_SERVER["DOCUMENT_ROOT"] . "/services/logger.php");
    req_log();
    include_once($data["file_path"]);
    $plugin = new $data["class"];
    $plugin->run($_REQUEST);
} else {
    code(405);
}
unset($plugin);
