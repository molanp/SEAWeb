<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/until.php");
if (!tokentime($_COOKIE)) {
    die(include_once($_SERVER["DOCUMENT_ROOT"] . "/sw-ad/index.php"));
}
?>
<!DOCTYPE html>
<html lang="zh-cn">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://registry.npmmirror.com/mdui/2.0.3/files/mdui.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/mark.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="/assets/js/jquery-3.7.1.min.js"></script>
    <script src="/assets/js/marked.min.js"></script>
    <script src="/assets/js/login.js"></script>
    <script src="/assets/js/cookie.js"></script>
    <script src="/assets/js/theme.js"></script>
    <script src="/assets/js/backstage.js"></script>
    <script src="/assets/js/windows.js"></script>
    <script src="/assets/js/chart-4.4.0.min.js"></script>
    <title>后台管理</title>
</head>

<body style="overflow: hidden;">
    <mdui-layout>
        <mdui-top-app-bar>
            <mdui-buutton-icon icon="home"></mdui-buutton-icon>
            <mdui-top-app-bar-title>Web管理</mdui-top-app-bar-title>
            <div style="flex-grow: 1"></div>
            <mdui-dropdown>
                <mdui-button-icon slot="trigger" icon="menu"></mdui-button-icon>
                <mdui-menu>
                    <mdui-menu-item>
                        <mdui-icon name="account_circle"></mdui-icon>
                        <?= $_COOKIE["user"] ?>
                    </mdui-menu-item>
                    <mdui-menu-item href="javascript:resetpassword()">
                        <mdui-icon name="lock"></mdui-icon>
                        修改密码
                    </mdui-menu-item>
                    <mdui-menu-item href="javascript:loginout()">
                        <mdui-icon name="exit_to_app"></mdui-icon>
                        注销
                    </mdui-menu-item>
                </mdui-menu>
            </mdui-dropdown>
        </mdui-top-app-bar>
        <mdui-navigation-rail value="dash" contained divider>
            <mdui-navigation-rail-item value="dash" icon="dashboard" href="/sw-ad/">主面板</mdui-navigation-rail-item>
            <mdui-navigation-rail-item value="api" icon="api" href="/sw-ad/api">API管理</mdui-navigation-rail-item>
            <mdui-navigation-rail-item value="settings" icon="settings" href="/sw-ad/settings">设置</mdui-navigation-rail-item>
            <mdui-navigation-rail-item value="web" icon="edit_note" href="/sw-ad/web">修改信息</mdui-navigation-rail-item>
            <mdui-navigation-rail-item value="log" icon="description" href="/sw-ad/log">日志分析</mdui-navigation-rail-item>
        </mdui-navigation-rail>
        <mdui-layout-main>
            <div id="data"  style="height: calc(100vh - 10px);overflow: auto;">
                <mdui-circular-progress></mdui-circular-progress>
            </div>
        </mdui-layout-main>
    </mdui-layout>
    <script src="https://registry.npmmirror.com/mdui/2.0.3/files/mdui.global.js"></script>
</body>

</html>