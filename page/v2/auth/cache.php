<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/until.php");

req_log();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once($_SERVER["DOCUMENT_ROOT"] . "/services/connect.php");
    $data = $_POST;
    if (isset($data["token"])) {
        if (tokentime($data)) {
            $DATABASE->exec("TRUNCATE api");
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
