<?php
include_once('../services/Config.class.php');
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
    <html lang="zh-CN">
    
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" />
        <meta name="renderer" content="webkit" />
        <meta name="force-rendering" content="webkit" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    
        <link rel="Shortcut Icon" href="../favicon.ico">
        <link rel="bookmark" href="../favicon.ico" type="image/x-icon" />   
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/css/mdui.min.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/js/mdui.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.17/sweetalert2.all.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <script src="/public/js/reg.js"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC&display=swap" rel="stylesheet">
        <title>注册页面</title>
        <style>
            body {
                font-family: "Noto Sans SC", sans-serif;
                background: url("https://pic3.zhimg.com/v2-230b33bf409772ea9a62be47d25cf816_r.jpg");
                background-size: cover;
                background-position: center center;
                background-attachment: fixed;
                background-repeat: no-repeat;
                padding-bottom: 100px;
            }
    
            #login-box {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                border: solid 1px rgba(0, 0, 0, 0.2);
                box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
                padding: 33px 33px 33px 33px;
                min-width: 300px;
                background-color: #ffffff80;
                text-align: center;
                line-height: 40px;
                backdrop-filter: blur(20px);
            }
    
            .login-button {
                margin-top: 20px;
                width: 190px;
                height: 30px;
                font-size: 20px;
                font-weight: 600;
                color: #ffffff;
                background-image: linear-gradient(to right, #74ebd5 0%, #9face6 100%);
                border: 0;
                border-radius: 5px;
                line-height: 1.7rem;
            }
    
            #footer {
                position: fixed;
                bottom: 0;
                width: 100%;
                height: 60px;
                line-height: 10px;
                text-align: center;
                box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
                background-color: #ffffff20;
                backdrop-filter: blur(20px);
            }
    
            p {
                color: #525252;
            }
        </style>
    </head>
    
    <body>
        <div id="login-box">
            <h1 id="regtitle" style="color: #525252;">注册</h1>
            <a>点击注册后请等待2秒左右反馈</a>
            <br>
            <a>1.基岩版注册成功后将自动分配服务端 版本为1.19.50 请自行改端口直接开服使用即可</a>
            <!--<h3 id="subtitle" style="color: #6d6d6d;">客服链接</h3>-->
            <div class="form">
                <div class="mdui-textfield">
                    <input id="username" class="mdui-textfield-input" type="text" placeholder="用户名" required />
                    <div class="mdui-textfield-error">用户名不能为空</div>
                </div>
                <marquee  behavior="scroll">1.注册的账户尽量为QQ号或者微信号 或者罕见中文 如果您已经拥有面板账号，请勿在此注册
                    2.注册密码必须为9-36字符包含大小写及数字 </marquee>
                <div class="mdui-textfield">
                    <input id="password" class="mdui-textfield-input" type="password" placeholder="密码" required />
                    <div class="mdui-textfield-error">密码不能为空</div>
                </div>
                <div class="mdui-textfield">
                    <input id="captcha" class="mdui-textfield-input" type="text" placeholder="验证码" required />
                    <div class="mdui-textfield-error">验证码不能为空</div>
                </div>
                <select id="type" name="groups">
                    <option selected>BDS服务端</option>
                    <option>LLBDS服务端</option>
                    <option>JAVA17服务端</option>
                </select>
                <br>
                <a>2.java16 8由于Windows容器问题，暂停开通 如果需要请购买付费Linux节点|每月仅需9.9元</a>
            </div>
            <button id="login-btn" onclick="register()"
                class="login-button mdui-btn mdui-btn-raised mdui-ripple">注册</button>
                <p id="copyright">&copy;Copyright 2022 蓝天云公益&trade;</p><br>
        </div>
        <!--<div id="footer">
            <p id="copyright">&copy;Copyright 2022 蓝天云公益&trade;</p><br>
        <</div>-->
    </body>
    
    </html>';}
?>