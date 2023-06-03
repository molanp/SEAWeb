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
}/*
if (isset($_POST["newpassword"]) && isset($_POST["repassword"]) && !empty($_POST))
{
if (!preg_match("/^[a-zA-Z0-9]*$/",$_POST["newpassword"]))
    {
        echo '<script>alert("只允许字母和数字");</script>'; 
    }else if (empty($_POST["repassword"]))
    {
        echo '<script>alert("密码不得为空");</script>';
    } else if(!preg_match("/^[a-zA-Z0-9]*$/",$_POST["repassword"]))
        {
            echo '<script>alert("只允许字母和数字");</script>'; 
        }else if($_POST['newpassword']!=$_POST['repassword']) {
            echo '<script>alert("两次密码输入不一致，请重新输入！");</script>'; 
        }else if($_POST['newpassword']==$_POST['repassword'] && isset($_POST["newpassword"])) {
            $DATA->set("account",["username"=> $_COOKIE['user'],"password"=>base64_encode($_POST['newpassword'])])->save();
            //die("<script>alert('修改成功，请重新登录！');window.location.reload();</script>");
        }
    }
*/
?>