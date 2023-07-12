<?php
include_once('services/until.php');

load();
$web = curl_get('http://'.$_SERVER['HTTP_HOST'].'/v2/info',["for"=>"web"]);
$web = ($web["status"] != 200) ? die($web["data"]) : $web["data"];
?>

<!DOCTYPE html>
<html>
<html lang="zh-CN">
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
</head>
<body class="mdui-drawer-body-left" style="padding-top: 20px;">
    <button onmouseover="goout(this)" onmouseout="goin(this)" id="aside_btn"></button>
    <div class="mdui-drawer" id="drawer">
    <ul class="mdui-list">
        <li class="mdui-text-color-theme mdui-text-center mdui-typo-display-1" name="title">Loading...</li>
        <li class='mdui-subheader mdui-text-center'>Version&nbsp;<span name="version">Loading...</span></li>
        <li class="mdui-list-item mdui-ripple">
                <a class="mdui-list-item-content" href="/">
                    <i class="mdui-icon material-icons">home</i>主页
                </a>
            </li>
            <li class="mdui-list-item mdui-ripple">
                <a class="mdui-list-item-content" href="javascript:changeTheme()">
                    <i class="mdui-icon material-icons">brightness_medium</i>夜间模式
                </a>
            </li>
            <li class='mdui-subheader'>管理入口</li>
            <li class="mdui-list-item mdui-ripple">
                <a class="mdui-list-item-content" href="/admin">
                    <i class="mdui-icon material-icons">exit_to_app</i>登录
                </a>
            </li>
            <span name="sider_list">Loading...</span>
            <li class="mdui-subheader mdui-text-center" name="copyright"></li>
        </ul>
        <hr>

    </div>
    <div class="mdui-text-color-theme mdui-typo-display-1 mdui-text-center" name="title">Loading...</div>
    <div id="box">
        <h3 class="mdui-text-color-theme article-title"><i class="mdui-icon material-icons mdui-text-color-blue">star_border</i>网站简介</h3>
        <p name="index_description">Loading...</p>
    </div>
    <div id="box">
        <h3 class="mdui-text-color-theme article-title"><i class="mdui-icon material-icons mdui-text-color-orange">announcement</i>公告<code><span name="latesttime"></span></code></h3>
        <p name="notice">Loading...</p>
    </div>
    <div id="box">
            <h3 class="mdui-text-color-theme article-title"><i class="mdui-icon material-icons mdui-text-color-blue">link</i>友情链接</h3>
            <span name="links"></span>
    </div>
    <div id="footer">
        <p name="record">Loading...</p>
        <p id="copyright"><span name="copyright">Loading...</span>&nbsp;.&nbsp;Power by <a href="https://github.com/molanp">molanp</a></p>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/js/mdui.min.js"></script>
</body>
</html>