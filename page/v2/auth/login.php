<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/services/until.php");

req_log();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    RequestLimit("10/min");
    include_once($_SERVER["DOCUMENT_ROOT"]."/services/connect.php");
    $data = $_POST;
    $type = $_POST["type"] ?? NULL;
    switch($type) {
        case "pass":
            $token = $data["token"];
            $pwd = hash("sha256", $data["new"]);
            if (tokentime($token)) {
                if ($pwd !== hash("sha256",$data["again"])) {
                    _return_("两次输入密码不同",406);
                } else {
                    $stmt = $DATABASE->prepare("UPDATE users SET password = :password WHERE token = :token");
                    $stmt->bindParam(':password', $pwd);
                    $stmt->bindParam(':token', $token);
                    $stmt->execute();
                    code(200);
                }
            } else {
                code(401);
            }
            break;
        default:
            if (isset($data["password"]) && isset($data["username"])) {
                $usr = $data["username"];
                $pwd = hash("sha256",$data["password"]);
                $stmt = $DATABASE->prepare("SELECT password FROM users WHERE username = :username");
                $stmt->bindParam(':username', $usr);
                $stmt->execute();
                $result = $stmt->fetchColumn();
                if ($result == $pwd) {
                    $token = uniqid("swb_");
                    $hashedToken = hash("sha256", $token);
                    $currentTime = time();
                    $stmt = $DATABASE->prepare("UPDATE users SET token = :token, logtime = :time WHERE username = :username");
                    $stmt->bindParam(':token', $hashedToken);
                    $stmt->bindParam(':time', $currentTime);
                    $stmt->bindParam(':username', $usr);
                    $stmt->execute();
                    _return_([
                        "user"=>$data["username"],
                        "token"=> $hashedToken
                    ]);
                } else {
                    _return_("账号或密码错误", 400);
                }
            } else {
                code(400);
            }
    }
}
?>
