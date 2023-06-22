<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/css/mdui.min.css" />
    <link rel="stylesheet" href="/assets/css/mark.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.17/sweetalert2.all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="/assets/js/login.js"></script>
    <script src="/assets/js/backstage.js"></script>
    <title>后台管理</title>
</head>
<body class="mdui-appbar-with-toolbar mdui-drawer-body-left">
<div class="mdui-drawer mdui-drawer-close mdui-drawer-open" id="sider">
        <ul class="mdui-list">
        <li class="mdui-list-item mdui-ripple">
            <div class="mdui-list-item-content">
                <a href="#">主面板</a>
            </div>
        </li>
        <li class="mdui-list-item mdui-ripple">
            <div class="mdui-list-item-content">
                <a href="#status">api控制</a>
            </div>
        </li>
        </ul>
    </div>
    <div class="mdui-appbar mdui-appbar-fixed mdui-toolbar mdui-color-blue">
        <a href="javascript:window.location.href=window.location.origin" class="mdui-btn mdui-btn-icon" mdui-tooltip="{content: 'Back Home', position: 'auto'}">
            <i class="mdui-icon material-icons">home</i>
        </a>
        <span class="mdui-typo-title">后台管理</span>
        <div class="mdui-toolbar-spacer"></div>
        <a href="#" class="mdui-btn mdui-btn-icon" mdui-menu="{target: '#menu'}">
            <i class="mdui-icon material-icons">menu</i>
        </a>
        <ul class="mdui-menu" id="menu">
        <li class="mdui-menu-item">
            <a href="#" class="mdui-ripple">
            <i class="mdui-menu-item-icon mdui-icon material-icons">account_circle</i><?=$_COOKIE['user']?>
            </a>
        </li>
        <li class="mdui-menu-item">
            <a href="javascript:resetpassword()" class="mdui-ripple">
            <i class="mdui-menu-item-icon mdui-icon material-icons">lock</i>Reset Password
            </a>
        </li>
        <li class="mdui-divider"></li>
        <li class="mdui-menu-item">
            <a href="javascript:loginout()" class="mdui-ripple">
            <i class="mdui-menu-item-icon mdui-icon material-icons">exit_to_app</i>Logout
            </a>
        </li>
        </ul>
    </div>
    <div class="container mdui-container-fluid">
        <span class="content" style="width: 75%;padding: 20px;background-color: #fff;">Loading...</span>
    </div>
    <button class="mdui-fab mdui-fab-fixed  mdui-color-pink" onclick="sider()"><i class="mdui-icon material-icons">add</i></button>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/js/mdui.min.js"></script>
</body>

</html>