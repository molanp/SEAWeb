<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');

load();

include_once($_SERVER['DOCUMENT_ROOT'].'/services/connect.php');
if ($database->query("SELECT value FROM setting WHERE item = 'maintenance_mode'")->fetchColumn() == 'true') {
    die(include_once('maintenance.html'));
};
$sql = "SELECT name, profile FROM api WHERE url_path = :urlPath";
$statement = $database->prepare($sql);
$statement->execute([":urlPath" => $_SERVER['REQUEST_URI']]);
$data = $statement->fetch(PDO::FETCH_ASSOC);
if($data==null) {
    die(include_once($_SERVER['DOCUMENT_ROOT'].'/404.php'));
}
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
    <meta name="keywords" content="<?=$web["keywords"]?>">
    <meta name="description" content="<?= str_replace("\n", "", strip_tags($data['profile']))?>">
    <link rel="stylesheet" href="https://font.sec.miui.com/font/css?family=MiSans:400,500,600,700:Chinese_Simplify,Latin,Chinese_Traditional&amp;display=swap">
    <link rel="Shortcut Icon" href="/favicon.ico">
    <link rel="bookmark" href="/favicon.ico" type="image/x-icon" /> 
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/mark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/css/mdui.min.css" />
    <script src="https://cdn.bootcss.com/marked/5.0.4/marked.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="/assets/js/purify.min.js"></script>
    <script src="/assets/js/api.js"></script>
    <script src="/assets/js/notice.js"></script>
    <title><?= $data["name"]." - ".$web["index_title"]?></title>
</head>

<body class="mdui-appbar-with-toolbar mdui-theme-primary-light-blue mdui-theme-accent-light-blue" id="top">

    <header class="mdui-appbar-fixed mdui-appbar mdui-color-white">
        <div class="mdui-color-white mdui-toolbar">
            <a class="mdui-typo-headline mdui-hidden-xs" href="/" id="title">title</a>
            <span class="mdui-typo-title" id="version">version</span>
            <div class="mdui-toolbar-spacer"></div>
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
    <div class="mdui-m-b-2" id="doc">
        <div class="mdui-text-center mdui-color-theme-a200 mdui-text-color-white mdui-m-b-2" style="padding-top:80px;padding-bottom:80px;">
            <noscript>
                <div style="text-align: center;margin-top: 10%;">
                    <h4>Sorry, the web page requires a Javascript runtime environment, please allow you to run scripts or use a new version of the modern browser.</h4>
                    <p>It is recommended to use <a href="https://www.google.cn/chrome/">Chrome</a> modern browser.</p>
                </div>
            </noscript>
            <div class="mdui-typo-display-1"><span name="api_name">Loading...</span></div>
            <br/>
            <div class="mdui-chip" mdui-tooltip="{content: 'API Version', position: 'top'}">
                <span class="mdui-chip-title"><i class="mdui-icon material-icons mdui-text-color-blue">info_outline</i><span name="api_version">Loading...</span></span>
            </div>
            <div class="mdui-chip" mdui-tooltip="{content: 'API Author', position: 'top'}">
                <span class="mdui-chip-title"><i class="mdui-icon material-icons mdui-text-color-blue">account_circle</i><span name="author">Loading...</span></span>
            </div>
            <div class="mdui-chip" mdui-tooltip="{content: 'API Count', position: 'top'}">
                <span class="mdui-chip-title"><i class="mdui-icon material-icons mdui-text-color-blue">equalizer</i><span name="api_count">Loading...</span> times</span>
            </div>
        </div>
        <div class="mdui-container">
            <div class="mdui-row">
                <div class="mdui-col-md-6">
                <div class="mdui-card mdui-hoverable mdui-m-y-2" style="border-radius:10px">
                    <div class="mdui-card-primary">
                    <div class="mdui-card-primary-title">
                    <i class="mdui-icon material-icons mdui-text-color-blue">language</i>简介
                    </div>
                    </div>
                    <div class="mdui-card-content">
                    <span name="api_profile">Loading...</span>
                    </div>
                </div>
                </div>
                <div class="mdui-col-md-6">
                <div class="mdui-card mdui-hoverable mdui-m-y-2" style="border-radius:10px">
                    <div class="mdui-card-primary">
                    <div class="mdui-card-primary-title">
                    <i class="mdui-icon material-icons mdui-text-color-orange">view_compact</i>API 地址
                    </div>
                    </div>
                    <div class="mdui-card-content">
                    <span name="api_address">Loading...</span>
                    </div>
                </div>
                </div>
                <div class="mdui-col-md-6">
                <div class="mdui-card mdui-hoverable mdui-m-y-2" style="border-radius:10px">
                    <div class="mdui-card-primary">
                    <div class="mdui-card-primary-title">
                    <i class="mdui-icon material-icons mdui-text-color-purple">vpn_key</i>参数列表 (打<code>*</code>是必填项)
                    </div>
                    </div>
                    <div class="mdui-card-content">
                    <span name="request">Loading...</span>
                    </div>
                </div>
                </div>
                <div class="mdui-col-md-6">
                <div class="mdui-card mdui-hoverable mdui-m-y-2" style="border-radius:10px">
                    <div class="mdui-card-primary">
                    <div class="mdui-card-primary-title">
                    <i class="mdui-icon material-icons mdui-text-color-gray">reply</i>返回的数据
                    </div>
                    </div>
                    <div class="mdui-card-content">
                    <span name="response">Loading...</span>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="foot mdui-text-center mdx-footer-morden">
        <span name="record"></span>  
        <span name="copyright"></span>                
    </footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/js/mdui.min.js"></script>
</body>
</html>