<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/Config.class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');
$DATA = new Config($_SERVER['DOCUMENT_ROOT'].'/db/db');
$account = $DATA->get('account');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $_POST;
    $type = $_POST["type"] ?? NULL;
    switch($type) {
        case 'pass':
            $token = trim($data['token']);
            if ($token == $account['password']) {
                if ($_POST["new"] !== $_POST["again"]) {
                    _return_('两次输入密码不同',400);
                } else {
                    $DATA->set('account',["username"=>$account["username"],"password"=>hash('sha256', $_POST["new"])])->save();
                    _return_('密码已修改，请重新登录');
                }
                //_return_($data);
            } else {
                _return_('身份验证失败',403);
            }
            break;
        default:
            $username = trim($data['username']) ?? NULL;
            $password = trim($data['password']) ?? NULL;
            if ($username == $account['username'] && hash('sha256',$password) == $account['password']) {
                _return_(["user"=>$data["username"],"token"=>hash('sha256',(trim($data['password'])))]);
                }
            else {
                _return_(["user"=>$data["username"],"token"=>hash('sha256',(trim($data['password'])))]);
            }
            break;
}
}
?>