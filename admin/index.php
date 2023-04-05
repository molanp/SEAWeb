<?php
include_once('../Config.class.php');
$DATA = new Config('../db/db');
$account = $DATA->get('account');
// 处理用户登录信息
if (isset($_COOKIE['token']) && $_COOKIE['token'] == $account['password']) {
    die(include_once('../page/backstage.php'));
} elseif (isset($_POST['login']) && trim($_POST['username']) == $account['username'] && (base64_encode(trim($_POST['password'])) == $account['password'])) {
    setcookie('user',trim($_POST['username']),time()+1800);
    setcookie('token',base64_encode(trim($_POST['password'])),time()+1800);
    include_once('../page/backstage.php');
    }
else {
    echo '<!DOCTYPE html>
<html>
<head>
    <title>登录</title>
    <meta name="content-type"; charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.17/sweetalert2.all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
</head>
<body>
<style>
body
{
    margin: 0;
    padding: 0;
    background: url("https://pic3.zhimg.com/v2-230b33bf409772ea9a62be47d25cf816_r.jpg") no-repeat center;
    background-size: cover;
    -webkit-background-attachment: fixed;
    background-attachment: fixed;
}
#bigBox
{
    margin: 0 auto;	/* login框剧中 */
    margin-top: 170px; /* login框与顶部的距离 */
    padding: 20px 50px;	/* login框内部的距离(内部与输入框和按钮的距离) */
    width: 40%;
    height: 80%;
    text-align: center;	/* 框内所有内容剧中 */
    background-color: rgba(89, 89, 89, 0.05);
    backdrop-filter: blur(30px);
    -webkit-backdrop-filter: blur(30px);
    border: 0.8px solid rgba(255, 255, 255, 0.18);
    box-shadow: rgba(14, 14, 14, 0.19) 0px 6px 15px 0px;
    -webkit-box-shadow: rgba(14, 14, 14, 0.19) 0px 6px 15px 0px;
    border-radius: 25px;
    -webkit-border-radius: 25px;
    color: rgba(128, 128, 128, 0.2);
                
}
#bigBox h1
{
    color: white;	/* LOGIN字体颜色 */
    font-family: "Comic Sans MS";
}
#bigBox .inputBox
{
    margin-top: 20px;	/* 输入框顶部与LOGIN标题的间距 */
}
#bigBox .inputBox .inputText
{
    margin-top: 20px;	/* 输入框之间的距离 */
}
#bigBox .inputBox .inputText input
{
    border: 0;	/* 删除输入框边框 */
    padding: 10px 10px;	/* 输入框内的间距 */
    border-bottom: 1px solid white;	/* 输入框白色下划线 */
    background-color: #00000000;	/* 输入框透明 */
    color: white;	/* 输入字体的颜色 */
}
#bigBox .loginButton
{

    margin-top: 40px;	/* 按钮顶部与输入框的距离 */
    width: 50%;
    height: 50%;
    color: white;	/* 按钮字体颜色 */
    border: 0; /* 删除按钮边框 */
    border-radius: 20px;	/* 按钮圆角边 */
    background-image: linear-gradient(to right, #b8cbb8 0%, #b8cbb8 0%, #b465da 0%, #cf6cc9 33%, #ee609c 66%, #ee609c 100%);	/* 按钮颜色 */
}
.m-left{
    margin-left: 30px;

}
.register{
    position: absolute;
    margin-bottom: 1000px;
    right: 10px;
    color: #ffffff;
    /*left:  calc(5% - 200px);*/
    margin-right:800px;
    /*bottom: 240px;*/
    font-size: 13px;
}
.fgtpwd{
    position: absolute;
    margin-bottom: 1000px;
    right: 10px;
    color: #ffffff;
    /*left:  calc(5% - 200px);*/
    margin-right:666px;
    /*bottom: 240px;*/
    font-size: 13px;
}

</style>
<div id="bigBox">
        <h1>登录</h1>
        <form id="loginform" method="post">
            <div class="inputBox">
                    <div class="inputText">
                        <input type="text" id="name" name="username" placeholder="用户名" value="">
                    </div>
                <div class="inputText">
                   <input type="password" id="password" name="password" placeholder="密码">
                <br >
                </div>
                <div>
                    <input type="submit" id="login" name="login" value="登录" class="loginButton m-left">
                </div>
            </div>
        </form>
</div>
</body>
</html>';}
?>