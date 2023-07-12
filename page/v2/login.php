<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/Config.class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');
$DATA = new Config($_SERVER['DOCUMENT_ROOT'].'/data/web');
$account = $DATA->get('account');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    RequestLimit('10/min','login');
    $data = $_POST;
    $type = $_POST["type"] ?? NULL;
    switch($type) {
        case 'pass':
            $token = $data['token'];
            if ($token == $account['password']) {
                if ($data["new"] !== $data["again"]) {
                    _return_('两次输入密码不同',400);
                } else {
                    $DATA->set('account',["username"=>$account["username"],"password"=>hash('sha256', $data["new"])])->save();
                    _return_('密码已修改，请重新登录');
                }
            } else {
                _return_('身份验证失败',403);
            }
            break;
        default:
            if (isset($data['password']) && isset($data['username'])) {
                if (hash('sha256', $data['password']) === $account['password'] && $data['username'] === $account['username']) {
                    _return_([
                        "login"=>"success",
                        "user"=>$data["username"],
                        "token"=>hash('sha256',($data['password']))
                    ]);
                } else {
                    _return_([
                        "login"=>"failed",
                        "user"=>$data["username"],
                        "token"=>hash('sha256',($data['password']))
                    ]);
                }
            } else {
                _return_('Bad Request',400);
            }
    }
}
?>