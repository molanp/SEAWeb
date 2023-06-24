<?php
include_once('../services/Config.class.php');

$DATA = new Config('../data/web');
$account = $DATA->get('account');
if (isset($_COOKIE['token']) && $_COOKIE['token'] == $account['password']) {
    die(include_once('../page/backstage.html'));
} else {?>
    <!DOCTYPE html>
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
        <script src="/assets/js/login.js"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC&display=swap" rel="stylesheet">
        <title>登录</title>
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
            <h1 id="regtitle" style="color: #525252;">登入</h1>
            <a href="javascript:window.location.href=window.location.origin">返回网站</a>
            <div class="form">
                <div class="mdui-textfield">
                    <input id="username" class="mdui-textfield-input" type="text" placeholder="用户名" required />
                    <div class="mdui-textfield-error">用户名不能为空</div>
                </div>
                <div class="mdui-textfield">
                    <input id="password" class="mdui-textfield-input" type="password" placeholder="密码" required />
                    <div class="mdui-textfield-error">密码不能为空</div>
                </div>
                <br>
            </div>
            <button id="login-btn" onclick="login()"
                class="login-button mdui-btn mdui-btn-raised mdui-ripple">登入</button>
        </div>
    </body>
    </html>
    <?php }?>