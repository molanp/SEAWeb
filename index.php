<?php
if (file_exists("install.php")) {
    die(include("install.php"));
}

include_once("services/until.php");
include_once("services/Config.class.php");

include_once("services/connect.php");
if ($DATABASE->query("SELECT value FROM setting WHERE item = '维护模式'")->fetchColumn() == "true") {
    die(include_once("page/maintenance.html"));
};
$web = new Config($_SERVER["DOCUMENT_ROOT"] . "/data/web");
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
    <meta name="description" content="<?= str_replace("\n", "", strip_tags($web["index_description"])); ?>">
    <meta name="keywords" content="<?= $web["keywords"]; ?>">
    <link rel="Shortcut Icon" href="/favicon.ico">
    <link rel="bookmark" href="/favicon.ico" type="image/x-icon" />
    <link href="https://unpkg.com/mdui/mdui.css" rel="stylesheet">
    <script src="/assets/js/marked.min.js"></script>
    <script src="/assets/js/jquery-3.7.1.min.js"></script>
    <script src="/assets/js/purify.min.js"></script>
    <script src="/assets/js/cookie.js"></script>
    <script src="/assets/js/bar.js"></script>
    <script src="/assets/js/app.js"></script>
    <title><?= $web["index_title"] ?></title>
</head>

<body>
    <mdui-top-app-bar scroll-behavior="elevate" id="bar"></mdui-top-app-bar>
    <div style="text-align:center;">
        <br>
        <h3 id="title"></h3>
        <span id="index_description"><mdui-circular-progress></mdui-circular-progress></span>
        <br>
        <span id="links"></span>
    </div>
    <mdui-linear-progress></mdui-linear-progress>
    <noscript>
        <div style="text-align: center;margin-top: 10%;">
            <h4>Sorry, the web page requires a Javascript runtime environment, please allow you to run scripts or use a new version of the modern browser.</h4>
            <p>It is recommended to use <a href="https://www.microsoft.com/edge/">Edge</a> modern browser.</p>
        </div>
    </noscript>
    <div id="app_api" class="container">
        <br>
            <mdui-circular-progress ></mdui-circular-progress>
    </div>
    <div id="lazyload" class="container"></div>

    <footer style="text-align: center;margin-top: 10%;">
        <span id="record"></span>
        <span id="copyright"></span>
        <p>本站内容由网友上传(或整理自网络)，原作者已无法考证，版权归原作者所有。仅供学习参考，其观点不代表本站立场，网站接口数据均收集互联网。</p>
    </footer>
    <script src="https://unpkg.com/mdui/mdui.global.js"></script>
</body>

</html>