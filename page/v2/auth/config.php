<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/until.php");

req_log();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = $_POST;
    if (isset($data["token"])) {
        if (tokentime($data)) {
            include_once($_SERVER["DOCUMENT_ROOT"] . "/services/config.php");
            $data = (new Config())->getAll();
            _return_($data);
        } else {
            code(401);
        }
    } else {
        code(400);
    }
} else {
    code(400);
}
