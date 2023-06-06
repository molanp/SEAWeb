<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/services/lock.php");
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/css/mdui.min.css" />
    <link rel="stylesheet" href="/assets/css/mark.css">
    <link rel="stylesheet" href="/assets/css/backstage.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/js/mdui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.17/sweetalert2.all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="/assets/js/login.js"></script>
    <script src="/assets/js/backstage.js"></script>
    <title>管理系统</title>
</head>
<body>
    <div class="navbar">
    <ul class="nav">
        <li><a href="javascript:window.location.href=window.location.origin">返回网站</a></li>
        <li><a href="javascript:loginout()">退出登录</a></li>
        <li><a href="javascript:resetpassword()">修改密码</a></li>
        <li>您好,<strong><?= $_COOKIE['user'];?></strong></li>
    </ul>
    </div>

    <div class="container">
        <div class="sidebar">
            <ul class="sidebar-nav">
            <li><a href="#" id="avtive">主面板</a></li>
            <li><a href="#status" id="avtive">api控制</a></li>
            </ul>
        </div>
        <div class="content">
        <blockquote>SEAWeb版本:<span name="version">114514</span>(最新版本:<span name="latest">Loading...</span>)</blockquote>
        <h3>修改网页信息</h3>
        <br>
        网站标题：<p><textarea id='editor' name='index_title'>Loading...</textarea></p>
        网站简介信息：<p><textarea id='editor' name='index_description'>Loading...</textarea></p>
        网站公告：<p><textarea id='editor' name='notice'>Loading...</textarea></p>
        网站底部版权信息：<p><textarea id='editor' name='copyright'>Loading...</textarea></p>
        网页备案号：<p><textarea id='editor' name='record'>Loading...</textarea></p>
        友情链接(一行一个)：示例： [链接1](http://xxx)<p><textarea id='editor' name='links'>Loading...</textarea></p>
        网站keywords(逗号分隔)：<p><textarea id='editor' name='keywords'>Loading...</textarea></p>
        <button onclick='save()'
        class='login-button mdui-btn mdui-btn-raised mdui-ripple'>保存</button>"
        </div>
    </div>
</body>

</html>