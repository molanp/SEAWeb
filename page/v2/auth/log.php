<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/until.php");

req_log();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (tokentime($_POST)) {
        switch($_POST["mode"]) {
            case 'log':
                include_once($_SERVER["DOCUMENT_ROOT"]."/services/connect.php");
                $page = $_POST["page"] ?? 1;
                if (!is_numeric($page) || $page <= 0 || floor($page) != $page) {
                    _return_("不合法的页码", 400);
                }
                $page = ($page - 1) * 20;
                $stmt = $DATABASE->prepare("SELECT time, level, content FROM log ORDER BY time DESC LIMIT 20 OFFSET $page");
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
            default:
                code(400);
        }
        _return_($data);
    } else {
        code(401);
    }
}