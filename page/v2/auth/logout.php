<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/until.php");

logger();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once($_SERVER["DOCUMENT_ROOT"] . "/services/connect.php");
    $data = $_POST;
    if (isset($data["token"])) {
        $token = $data["token"];
        if (tokentime($data)) {
            $DATABASE->exec("UPDATE users SET token = NULL WHERE token = '$token'");
            _return_("OK");
        } else {
            _return_("身份验证失败", 403);
        }
    } else {
        _return_("Bad Requests", 400);
    }
} else {
    _return_("Bad Requests", 400);
}
