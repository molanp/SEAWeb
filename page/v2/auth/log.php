<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/until.php");

req_log();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (tokentime($_POST)) {
        switch($_POST["mode"]) {
            case 'log':
                include_once($_SERVER["DOCUMENT_ROOT"]."/services/connect.php");
                $stmt = $DATABASE->prepare("SELECT time, level, content FROM log ORDER BY time DESC");
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
            default:
                _return_("未知的模式",400);
        }
        _return_($data);
    } else {
        _return_("身份验证失败",403);
    }
}