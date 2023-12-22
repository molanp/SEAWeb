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
            code(200);
        } else {
            code(401);
        }
    } else {
        code(400);
    }
} else {
    code(400);
}
