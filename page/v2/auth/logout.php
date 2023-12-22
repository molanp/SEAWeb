<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/until.php");

req_log();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once($_SERVER["DOCUMENT_ROOT"] . "/services/connect.php");
    $data = $_POST;
    if (isset($data["token"])) {
        $token = $data["token"];
        if (tokentime($data)) {
            $stmt = $DATABASE->prepare("UPDATE users SET token = NULL WHERE token = :token");
            $stmt->bindParam(':token', $token);
            $stmt->execute();
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
