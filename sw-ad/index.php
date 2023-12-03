<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/services/Config.class.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/services/connect.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/services/until.php");

if (isset($_COOKIE["token"], $_COOKIE["user"])) {
    $stmt = $DATABASE->prepare("SELECT token FROM users WHERE username = :username");
    $stmt->bindParam(":username", $_COOKIE["user"]);
    $stmt->execute();
    $storedToken = $stmt->fetchColumn();

    if ($storedToken && $storedToken === $_COOKIE["token"]) {
        die(include_once($_SERVER["DOCUMENT_ROOT"] . "/page/backstage.html"));
    } else {
        exit;
    }
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
        <link rel="stylesheet" href="https://unpkg.com/mdui@2.0.1/mdui.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="/assets/css/style.css">
        <script src="/assets/js/jquery-3.7.1.min.js"></script>
        <script src="/assets/js/login.js"></script>
        <script src="/assets/js/theme.js"></script>
        <script src="/assets/js/cookie.js"></script>
        <title>登录</title>
    </head>
    
    <body style="text-align:center;" class="mdui-theme-auto">
        <mdui-card style="width: 70%;height: 400px">
            <h2>登录</h2>
            <mdui-text-field label="账号" id="username" clearable></mdui-text-field>
            <mdui-text-field label="密码" id="password" type="password" toggle-password clearable></mdui-text-field>
            <mdui-button onclick="login()">登录</mdui-button>
            <br>
            <a href="javascript:window.history.back()">返回</a>
        </mdui-card>
    <script src="https://unpkg.com/mdui@2.0.1/mdui.global.js"></script>
    </body>
    </html><?php }?>