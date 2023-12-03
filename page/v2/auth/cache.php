<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/until.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once($_SERVER["DOCUMENT_ROOT"] . "/services/connect.php");
    $data = $_POST;
    if (isset($data["token"])) {
        $token = $data["token"];
        if (tokentime($data)) {
            $DATABASE->exec("DELETE FROM api");
            @unlink($_SERVER["DOCUMENT_ROOT"] . "/data/limit.php");
            @unlink($_SERVER["DOCUMENT_ROOT"] . "/sitemap.xml");
            _return_("OK");
        } else {
            _return_("莫得权限", 403);
        }
    } else {
        _return_("Bad Requests", 400);
    }
} else {
    _return_("Bad Requests", 400);
}
