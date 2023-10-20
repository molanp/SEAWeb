<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/Config.class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/connect.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');

if (isset($_COOKIE['token'],$_COOKIE['user']) && DATABASE->query("SELECT token FROM users WHERE username = '".$_COOKIE["user"]."'")->fetchColumn() == $_COOKIE['token']) {
    die(include_once($_SERVER['DOCUMENT_ROOT'].'/page/backstage.html'));
} else {?>
    <!DOCTYPE html>
    <html lang="zh-CN">
    
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" />
        <meta name="renderer" content="webkit" />
        <meta name="force-rendering" content="webkit" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <link rel="Shortcut Icon" href="/favicon.ico">
        <link rel="bookmark" href="/favicon.ico" type="image/x-icon" />   
        <link rel="stylesheet" href="/assets/css/mdui.min.css" />
        <script src="/assets/js/mdui.min.js"></script>
        <script src="/assets/js/jquery.min.js"></script>
        <script src="/assets/js/login.js"></script>
        <title>登录</title>
        <style>
            body {
            background-image: url('https://pic3.zhimg.com/v2-866187288d288e98779843e36fc0207e_r.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            }
            .login-container {
            width: 300px;
            margin: 0 auto;
            margin-top: 150px;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            }
        </style>
    </head>
    
    <body>
        <div class="mdui-container">
            <div class="mdui-card login-container">
                <form id="login-form">
                    <div class="mdui-textfield">
                    <label class="mdui-textfield-label">账号</label>
                    <input class="mdui-textfield-input" type="text" id="username" />
                    </div>
                    <div class="mdui-textfield">
                    <label class="mdui-textfield-label">密码</label>
                    <input class="mdui-textfield-input" type="password" id="password" />
                    </div>
                    <button class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-theme-accent" type="button" onclick="login()">登录</button>
                    <button class="mdui-btn mdui-btn-raised mdui-ripple" type="reset">清空</button>
                </form>
            </div>
        </div>
    </body>
    </html><?php }?>