<?php
include_once('services/until.php');
include_once('services/Config.class.php');

load();
$web = curl_get('http://'.$_SERVER['HTTP_HOST'].'/v2/info',["for"=>"web"]);
$web = ($web["status"] != 200) ? die($web["data"]) : $web["data"];
if ($web["setting"]["maintenance_mode"]===true) {
    die(include_once('page/maintenance.html'));
}
$web = $web["web"];
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" />
    <meta name="renderer" content="webkit" />
    <meta name="force-rendering" content="webkit" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="description" content="<?= str_replace("\n", "", strip_tags($web['index_description']));?>">
    <meta name="keywords" content="<?= $web["keywords"];?>">
    <link rel="stylesheet" href="https://font.sec.miui.com/font/css?family=MiSans:400,500,600,700:Chinese_Simplify,Latin,Chinese_Traditional&amp;display=swap">
    <link rel="Shortcut Icon" href="/favicon.ico">
    <link rel="bookmark" href="/favicon.ico" type="image/x-icon" /> 
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/mark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/css/mdui.min.css" />
    <script src="https://cdn.bootcss.com/marked/5.0.4/marked.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.17/sweetalert2.all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="/assets/js/purify.min.js"></script>
    <script src="/assets/js/app.js"></script>
    <title><?= $web["index_title"]?></title>

    <div class="mdui-text-color-white-text mdui-valign mdui-color-light-blue-200" style="height: 200px;">
            <div class="mdui-text-color-white mdui-center">
                <br/>
                <span class="mdui-typo-display-3" name="title">title</span>
                <span name="index_description">Loading...</span>
                <p><small>友情链接<span name="links"></span></small></p>
            </div>
    </div>

</head>
<body class="mdui-appbar-with-toolbar mdui-theme-primary-light-blue mdui-theme-accent-blue" id="top">
    <header class="mdui-appbar-fixed mdui-appbar mdui-color-white">
        <div class="mdui-color-white mdui-toolbar">
            <span class="mdui-typo-headline mdui-hidden-xs" name="title">title</span>
            <span name="version">version</span>
            <div class="mdui-toolbar-spacer"></div>
            <button mdui-tooltip="{content: '夜间模式', position: 'bottom'}" class="mdui-btn mdui-btn-icon"><i class="mdui-icon material-icons" onclick="changeTheme()">brightness_medium</i></button>
            <button mdui-menu="{target: '#main-menu'}" class="mdui-btn mdui-btn-icon"><i class="mdui-icon material-icons">more_vert</i></button>
            <ul class="mdui-menu" id="main-menu">
                <li class="mdui-menu-item" mdui-dialog="{target: '#login'}">
                    <a href="/admin" class="mdui-ripple">
                        <i class="mdui-menu-item-icon mdui-icon material-icons">person</i> 后台登录
                    </a>
                </li>
            </ul>
        </div>
    </header>
<div class="mdui-container mdui-p-x-2" id="main">
<div class="mdui-progress">
                <div class="mdui-progress-indeterminate"></div>
            </div>
    <!-- 展示所有接口 -->
    <div id="app_api">
        <div class="mdui-row">
            <div class="title">
                尚未任何接口
            </div>
            <div class="description">
                添加接口后，将显示在这里
            </div>
        </div>
    </div>
</div>

<a class="mdui-fab mdui-fab-fixed mdui-fab-hide mdui-ripple mdui-color-theme-accent" href="javascript:goTop();" id="fabUp" style="margin-bottom:60px;">
    <i class="mdui-icon material-icons">expand_less</i>
</a>
<footer class="foot mdui-text-center mdx-footer-morden">
    <span name="record"></span>  
    <span name="copyright"></span>                
</footer>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/js/mdui.min.js"></script>
</body>
</html>