<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/Config.class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    RequestLimit('10/min','login');
    include_once($_SERVER['DOCUMENT_ROOT'].'/services/connect.php');
    $data = $_POST;
    $type = $_POST["type"] ?? NULL;
    switch($type) {
        case 'pass':
            $token = $data['token'];
            $pwd = hash('sha256, $data["new"]');
            if ($database->query("SELECT username FROM users WHERE token = $token")->rowCount() > 0) {
                if ($pwd !== hash('sha256',$data["again"])) {
                    _return_('两次输入密码不同',400);
                } else {
                    $database->exec("UPDATE users SET password = '$pwd' WHERE token = '$token'");
                    _return_('密码已修改，请重新登录');
                }
            } else {
                _return_('身份验证失败',403);
            }
            break;
        default:
            if (isset($data['password']) && isset($data['username'])) {
                $usr = $data["username"];
                $pwd = hash('sha256',$data["password"]);
                if ($database->query("SELECT password FROM users WHERE username = '$usr'")->fetchColumn()==$pwd) {
                    $token = uniqid("swb_");
                    $database->exec("UPDATE users SET token = '$token', logtime = ".time()." WHERE username = '$usr'");
                    _return_([
                        "login"=>"success",
                        "user"=>$data["username"],
                        "token"=> $token
                    ]);
                } else {
                    _return_([
                        "login"=>"failed",
                        "msg"=>"账号或密码错误"
                    ]);
                }
            } else {
                _return_('Bad Request',400);
            }
    }
}
?>