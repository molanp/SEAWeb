<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/services/until.php');

include_once($_SERVER['DOCUMENT_ROOT'] . '/services/connect.php');
if ($DATABASE->query("SELECT value FROM setting WHERE item = '维护模式'")->fetchColumn() == 'true') {
    die(include_once('maintenance.html'));
};
$sql = "SELECT name, profile FROM api WHERE url_path = :urlPath";
$statement = $DATABASE->prepare($sql);
$statement->execute([":urlPath" => addSlashIfNeeded($_GET["__"])]);
$data = $statement->fetch(PDO::FETCH_ASSOC);
if ($data == null) {
    die(include_once($_SERVER['DOCUMENT_ROOT'] . '/404.php'));
}
$web = new Config($_SERVER['DOCUMENT_ROOT'] . '/data/web');
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
    <meta name="keywords" content="<?= $web["keywords"] ?>">
    <meta name="description" content="<?= str_replace("\n", "", strip_tags($data['profile'])) ?>">
    <link rel="Shortcut Icon" href="/favicon.ico">
    <link rel="bookmark" href="/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/mark.css">
    <link rel="stylesheet" href="https://unpkg.com/mdui@2.0.1/mdui.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="/assets/js/marked.min.js"></script>
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/purify.min.js"></script>
    <script src="/assets/js/api.js"></script>
    <script src="/assets/js/cookie.js"></script>
    <script src="/assets/js/theme.js"></script>
    <script src="/assets/js/query.js"></script>
    <script src="/assets/js/notice.js"></script>
    <title><?= $data["name"] . " - " . $web["index_title"] ?></title>
</head>

<body>
    <mdui-top-app-bar scroll-behavior="elevate">
        <mdui-top-app-bar-title>
            <span id="title" onclick="window.location.href='/'">title</span>
        </mdui-top-app-bar-title>
        <div style="flex-grow: 1"></div>
        <mdui-tooltip content="搜索">
            <mdui-button-icon href="javascript:output_search()" icon="search"></mdui-button-icon>
        </mdui-tooltip>
        <mdui-tooltip content="调用排行">
            <mdui-button-icon href="/page/rank.html" icon="equalizer"></mdui-button-icon>
        </mdui-tooltip>
        <mdui-tooltip content="公告">
            <mdui-button-icon mdui-tooltip="{content: '公告', position: 'bottom'}" onclick="notice()" icon="announcement"></mdui-button-icon>
        </mdui-tooltip>
        <mdui-tooltip content="夜间模式">
            <mdui-button-icon onclick="changeTheme()" icon="brightness_medium"></mdui-button-icon>
        </mdui-tooltip>
        <mdui-dropdown>
            <mdui-button-icon slot="trigger" icon="more_vert"></mdui-button-icon>
            <mdui-menu>
                <mdui-menu-item>
                    <mdui-button href="/sw-ad" icon="person">登录</mdui-button>
                </mdui-menu-item>
                <mdui-menu-item id="version"></mdui-menu-item>
            </mdui-menu>
        </mdui-dropdown>
    </mdui-top-app-bar>
    <noscript>
        <div style="text-align: center;margin-top: 10%;">
            <h4>Sorry, the web page requires a Javascript runtime environment, please allow you to run scripts or use a new version of the modern browser.</h4>
            <p>It is recommended to use <a href="https://www.microsoft.com/edge/download">Edge</a> modern browser.</p>
        </div>
    </noscript>
    <div style="text-align:center;">
        <br />
        <h3 id="api_name">
            <mdui-circular-progress></mdui-circular-progress>
        </h3>
        <mdui-tooltip content="API Version">
            <mdui-chip icon="info_outline">
                <span id="api_version">
                    <mdui-circular-progress></mdui-circular-progress>
                </span>
            </mdui-chip>
        </mdui-tooltip>
        <mdui-tooltip content="API Author">
            <mdui-chip icon="account_circle">
                <span id="author">
                    <mdui-circular-progress></mdui-circular-progress>
                </span>
            </mdui-chip>
        </mdui-tooltip>
        <mdui-tooltip content="API Count">
            <mdui-chip icon="equalizer">
                <span id="api_count">
                    <mdui-circular-progress></mdui-circular-progress>
                </span>
                &nbsp;times
            </mdui-chip>
        </mdui-tooltip>
    </div>
    <div class="container">
        <mdui-card class="item" variant="outlined">
            <h3>
                <i class="material-icons">language</i>
                简介
            </h3>
            <span id="api_profile" class="mdui-prose"></span>
        </mdui-card>
        <mdui-card class="item" variant="outlined">
            <h3><i class="material-icons">view_compact</i>API 地址</h3>
            <div class="mdui-table">
                <table id="api_address"></table>
            </div>
        </mdui-card>
        <mdui-card class="item" variant="outlined">
            <h3><i class="material-icons">vpn_key</i>参数列表 (红色是必填项)</h3>
            <div class="mdui-table">
                <table id="request"></table>
            </div>
        </mdui-card>
        <mdui-card class="item" variant="outlined">
            <h3><i class="material-icons">reply</i>返回的数据</h3>
            <div class="mdui-table">
                <table id="response"></table>
            </div>
        </mdui-card>
        <mdui-card class="item" variant="outlined">
            <h3><i class="material-icons">build</i>在线测试</h3>
            <div id="requestForm">
                <mdui-text-field readonly label="URL" id="urlInput"></mdui-text-field>
                <mdui-select class="mdui-select" id="methodSelect" value="GET" label="Method">
                    <mdui-menu-item value="GET">GET</mdui-menu-item>
                    <mdui-menu-item value="POST">POST</mdui-menu-item>
                    <mdui-menu-item value="PUT">PUT</mdui-menu-item>
                    <mdui-menu-item value="DELETE">DELETE</mdui-menu-item>
                    <mdui-menu-item value="OPTIONS">OPTIONS</mdui-menu-item>
                    <mdui-menu-item value="PATCH">PATCH</mdui-menu-item>
                </mdui-select>
            </div>
            <div class="mdui-table">
                <table id="paramsTable">
                    <thead>
                        <tr>
                            <th>参数名</th>
                            <th>值</th>
                            <th><a href="javascript:addParamRow()">添加参数</a></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <pre class="language-json" id="responseTEXT" style="text-align: left;"></pre>
            <mdui-button onclick="sendRequest()">发送请求</mdui-button>
    </div>
    </mdui-card>
    </div>
    <footer style="text-align: center;margin-top: 10%;">
        <span id="record"></span>
        <span id="copyright"></span>
        <p>本站内容由网友上传(或整理自网络)，原作者已无法考证，版权归原作者所有。仅供学习参考，其观点不代表本站立场，网站接口数据均收集互联网。</p>
    </footer>
    <script src="https://unpkg.com/mdui@2.0.1/mdui.global.js"></script>
</body>

</html>