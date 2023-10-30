<?php
if (file_exists("install.php")) {
    die(include("install.php"));
}
include_once('services/until.php');
include_once('services/Config.class.php');

include_once('services/connect.php');
if ($DATABASE->query("SELECT value FROM setting WHERE item = '维护模式'")->fetchColumn() == 'true') {
    die(include_once('page/maintenance.html'));
};
$web = new Config($_SERVER['DOCUMENT_ROOT'].'/data/web');
$web = $web->get("web");
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
    <link rel="Shortcut Icon" href="/favicon.ico">
    <link rel="bookmark" href="/favicon.ico" type="image/x-icon" /> 
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/mark.css">
    <link rel="stylesheet" href="/assets/css/mdui.min.css" />
    <script src="https://cdn.bootcss.com/marked/5.0.4/marked.min.js"></script>
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/purify.min.js"></script>
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/notice.js"></script>
    <script src="/assets/js/search.js"></script>
    <title><?= $web["index_title"]?></title>
</head>
<body class="mdui-appbar-with-toolbar mdui-theme-primary-light-blue mdui-theme-accent-blue" id="top">
    <div class="mdui-text-color-white-text mdui-valign mdui-color-light-blue-300" style="height: 200px;">
        <div class="mdui-center">
            <br/>
            <span class="mdui-typo-display-3 title">title</span>
            <span id="index_description">description</span>
            <p><small>友情链接<span id="links"></span></small></p>
        </div>
    </div>
    <header class="mdui-appbar-fixed mdui-appbar mdui-color-white">
        <div class="mdui-color-white mdui-toolbar">
            <span class="mdui-typo-headline title">title</span>
            <span class="mdui-typo-title mdui-hidden-xs" id="version">version</span>
            <div class="mdui-toolbar-spacer"></div>
            <button mdui-tooltip="{content: '调用排行', position: 'bottom'}" class="mdui-btn mdui-btn-icon"><i class="mdui-icon material-icons" onclick="window.location.href='/page/rank.html'">equalizer</i></button>
            <button mdui-tooltip="{content: '公告', position: 'bottom'}" onclick="notice()" class="mdui-btn mdui-btn-icon"><i class="mdui-icon material-icons">announcement</i></button>
            <button mdui-tooltip="{content: '夜间模式', position: 'bottom'}" class="mdui-btn mdui-btn-icon"><i class="mdui-icon material-icons" onclick="changeTheme()">brightness_medium</i></button>
            <button mdui-menu="{target: '#main-menu'}" class="mdui-btn mdui-btn-icon"><i class="mdui-icon material-icons">more_vert</i></button>
            <ul class="mdui-menu" id="main-menu">
                <li class="mdui-menu-item" mdui-dialog="{target: '#login'}">
                    <a href="/sw-ad" class="mdui-ripple">
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
    <noscript>
        <div style="text-align: center;margin-top: 10%;">
            <h4>Sorry, the web page requires a Javascript runtime environment, please allow you to run scripts or use a new version of the modern browser.</h4>
            <p>It is recommended to use <a href="https://www.microsoft.com/edge/">Edge</a> modern browser.</p>
        </div>
    </noscript>
    <div id="app_api">
        <div style="text-align: center;margin-top: 10%;">
            <h4>尚未有任何接口</h4>
            <p>添加接口后，将显示在这里</p>
        </div>
    </div>
</div>

<button class="mdui-fab mdui-fab-fixed mdui-ripple mdui-color-pink" onclick="output()">
  <i class="mdui-icon material-icons">search</i>
</button>
<footer class="foot mdui-text-center mdx-footer-morden">
    <span id="record"></span>
    <span id="copyright"></span>
    <p>本站内容由网友上传(或整理自网络)，原作者已无法考证，版权归原作者所有。仅供学习参考，其观点不代表本站立场，网站接口数据均收集互联网。</p>
</footer>
<script src="/assets/js/mdui.min.js"></script>
</body>
</html>