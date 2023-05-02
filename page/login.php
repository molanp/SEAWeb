<?php
if ($_GET) {
    die(print_r($_POST));
}
/*
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