<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');

load();

include_once($_SERVER['DOCUMENT_ROOT'].'/services/connect.php');
if (DATABASE->query("SELECT value FROM setting WHERE item = 'maintenance_mode'")->fetchColumn() == 'true') {
    die(include_once('maintenance.html'));
};
$sql = "SELECT name, profile FROM api WHERE url_path = :urlPath";
$statement = DATABASE->prepare($sql);
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
    <link rel="Shortcut Icon" href="/favicon.ico">
    <link rel="bookmark" href="/favicon.ico" type="image/x-icon" /> 
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/mark.css">
    <link rel="stylesheet" href="/assets/css/mdui.min.css" />
    <script src="https://cdn.bootcss.com/marked/5.0.4/marked.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="/assets/js/purify.min.js"></script>
    <script src="/assets/js/api.js"></script>
    <script src="/assets/js/query.js"></script>
    <script src="/assets/js/notice.js"></script>
    <title><?= $data["name"]." - ".$web["index_title"]?></title>
</head>

<body class="mdui-appbar-with-toolbar mdui-theme-primary-light-blue mdui-theme-accent-light-blue" id="top">

    <header class="mdui-appbar-fixed mdui-appbar mdui-color-white">
        <div class="mdui-color-white mdui-toolbar">
            <a class="mdui-typo-headline" href="/" id="title">title</a>
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
    <div class="mdui-m-b-2" id="doc">
        <div class="mdui-text-center mdui-color-theme-a200 mdui-text-color-white mdui-m-b-2" style="padding-top:80px;padding-bottom:80px;">
            <noscript>
                <div style="text-align: center;margin-top: 10%;">
                    <h4>Sorry, the web page requires a Javascript runtime environment, please allow you to run scripts or use a new version of the modern browser.</h4>
                    <p>It is recommended to use <a href="https://www.microsoft.com/edge/download">Edge</a> modern browser.</p>
                </div>
            </noscript>
            <div class="mdui-typo-display-1"><span id="api_name">Loading...</span></div>
            <br/>
            <div class="mdui-chip" mdui-tooltip="{content: 'API Version', position: 'top'}">
                <span class="mdui-chip-title"><i class="mdui-icon material-icons mdui-text-color-blue">info_outline</i><span id="api_version">Loading...</span></span>
            </div>
            <div class="mdui-chip" mdui-tooltip="{content: 'API Author', position: 'top'}">
                <span class="mdui-chip-title"><i class="mdui-icon material-icons mdui-text-color-blue">account_circle</i><span id="author">Loading...</span></span>
            </div>
            <div class="mdui-chip" mdui-tooltip="{content: 'API Count', position: 'top'}">
                <span class="mdui-chip-title"><i class="mdui-icon material-icons mdui-text-color-blue">equalizer</i><span id="api_count">Loading...</span> times</span>
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
                    <span id="api_profile">Loading...</span>
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
                    <div class="mdui-card-content mdui-table-fluid">
                    <table id="api_address" class="mdui-table mdui-table-hoverable"></table>
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
                    <div class="mdui-card-content mdui-table-fluid">
                    <table id="request" class="mdui-table mdui-table-hoverable"></table>
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
                    <div class="mdui-card-content mdui-table-fluid">
                    <table id="response" class="mdui-table mdui-table-hoverable"></table>
                    </div>
                </div>
                </div>
                <div class="mdui-col-md-6">
                <div class="mdui-card mdui-hoverable mdui-m-y-2" style="border-radius:10px">
                    <div class="mdui-card-primary">
                    <div class="mdui-card-primary-title">
                    <i class="mdui-icon material-icons mdui-text-color-teal-a400">build</i>在线测试
                    </div>
                    </div>
                    <div class="mdui-card-content">
                        <form id="requestForm">
                            <div class="mdui-textfield">
                            <label class="mdui-textfield-label">URL</label>
                            <span id="urlInput">window.location.pathname</span>
                            </div>

                            <div class="mdui-textfield">
                            <label class="mdui-textfield-label">Method</label>
                            <select class="mdui-select" id="methodSelect">
                                <option value="GET">GET</option>
                                <option value="POST">POST</option>
                                <option value="PUT">PUT</option>
                                <option value="DELETE">DELETE</option>
                                <option value="OPTIONS">OPTIONS</option>
                                <option value="PATCH">PATCH</option>
                                <!-- 可添加其他方法选项 -->
                            </select>
                            </div>

                            <table id="paramsTable" class="mdui-table">
                            <thead>
                                <tr>
                                <th>参数名</th>
                                <th>值</th>
                                <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- 参数行将在 JavaScript 中动态生成 -->
                            </tbody>
                            </table>
                            <label class="mdui-textfield-label">响应</label>
                            <pre><code class="language-json" id="responseTEXT"></code></pre>

                            <button class="mdui-btn mdui-btn-block mdui-color-theme-accent" type="button" onclick="addParamRow()">添加参数</button>

                            <div class="mdui-divider"></div>

                            <button class="mdui-btn mdui-btn-block mdui-color-theme" type="button" onclick="sendRequest()">发送请求</button>
                        </form>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="foot mdui-text-center mdx-footer-morden">
        <span id="record"></span>  
        <span id="copyright"></span>                
    </footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/js/mdui.min.js"></script>
</body>
</html>