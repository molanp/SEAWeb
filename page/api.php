<?php
define('IN_SYS', TRUE);
include_once('../services/mark.php');
include_once('../services/markExtra.php');
include_once('../services/until.php');

load();
$Parsedown = new ParsedownExtra();
$types = curl_get('http://'.$_SERVER['HTTP_HOST'].'/v2/info');
$types = ($types["status"] != 200) ? die($types["data"]) : $types["data"];
$goto = preg_replace('/\/i\//', '', $_SERVER["REQUEST_URI"]);

foreach(array_keys($types) as $type) {
    foreach(array_keys($types[$type]) as $p_name) {
        if ($goto == $types[$type][$p_name]['path']) {
            $api_name = $p_name;
            $data = $types[$type][$p_name];
        }
    }
}
if (!isset($data)) {
    die(header("location: http://".$_SERVER['HTTP_HOST']));
}
$status = $data['status'];
$api_profile = $Parsedown->setBreaksEnabled(true)->line($data['api_profile']);
$version = $data['version'];
$author = $data['author'];
$api_address = $Parsedown->setBreaksEnabled(true)->text($data['api_address']);
$request_parameters = $Parsedown->setBreaksEnabled(true)->text($data['request_parameters']);
$return_parameters = $Parsedown->setBreaksEnabled(true)->text($data['return_parameters']);
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
    <meta name="keywords" content="<?=$web["keywords"]?>">
    <meta name="description" content="<?= str_replace("\n", "", strip_tags($api_profile))?>">
    <link rel="stylesheet" href="https://font.sec.miui.com/font/css?family=MiSans:400,500,600,700:Chinese_Simplify,Latin,Chinese_Traditional&amp;display=swap">
    <link rel="Shortcut Icon" href="/favicon.ico">
    <link rel="bookmark" href="/favicon.ico" type="image/x-icon" /> 
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/mark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/css/mdui.min.css" />
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="/assets/js/app.js"></script>  
    <title><?= $api_name." - ".$web["index_title"]?></title>
</head>
<body class="mdui-drawer-body-left" style="padding-top: 20px;">
    <button onmouseover="goout(this)" onmouseout="goin(this)" id="aside_btn"></button>
    <div class="mdui-drawer" id="drawer">
        <ul class="mdui-list">
        <center>
            <h1 class="mdui-text-color-theme article-title" name="title">正在加载...</h1>
            <li class='mdui-subheader'><?= 'Version '.$web["version"]?></li>
        </center>
            <li class="mdui-list-item mdui-ripple">
                <div class="mdui-list-item-content" onclick="javascript:window.location.href=window.location.origin">
                <i class="mdui-icon material-icons">home</i>主页</a>
                </div>
            </li>
            <li class="mdui-list-item mdui-ripple">
                <div class="mdui-list-item-content" onclick="javascript:changeTheme()">
                    <i class="mdui-icon material-icons">brightness_medium</i>夜间模式
                </div>
            </li>
            <?php
            foreach(array_keys($types) as $type){
                echo "<li class='mdui-subheader'>$type</li>";
                foreach(array_keys($types[$type]) as $plugin){
                    if ($api_name == $plugin) {
                        echo "<li class='mdui-list-item mdui-ripple'>
                        <div class='mdui-list-item-content' id='active'>
                        &nbsp;".$Parsedown->setBreaksEnabled(true)->line($plugin)."</div>
                        </li>";
                    } else {
                        echo "<li class='mdui-list-item mdui-ripple'>
                        <div class='mdui-list-item-content' onclick='javascript:window.location.href=\"".$types[$type][$plugin]["path"]."\"'>&nbsp;".$Parsedown->setBreaksEnabled(true)->line($plugin)."</div>
                        </li>";
                    }
                }
            }
            ?>
            <li class='mdui-subheader'>管理入口</li>
            <li class="mdui-list-item mdui-ripple">
                <div class="mdui-list-item-content" onclick="javascript:window.location.href='/admin'">
                    <i class="mdui-icon material-icons">exit_to_app</i>登录
                </div>
            </li>
            <center>
                <li class="mdui-subheader">&copy;<?= $web['copyright']?></li>
            </center>
        </ul>
        <hr>

    </div>
    <h1 style="text-align:center;" class="mdui-text-color-theme article-title"><?= $api_name?></h1>
    <div id="box">
        <h3 class="mdui-text-color-theme article-title"><i class="mdui-icon material-icons mdui-text-color-blue">star_border</i>API 简介</h3>
        <p><?= $api_profile?></p>
        <p>
        <div class="mdui-chip" mdui-tooltip="{content: 'API Version', position: 'top'}">
            <span class="mdui-chip-title"><i class="mdui-icon material-icons mdui-text-color-blue">info_outline</i><?= $version?></span>
        </div>
        <div class="mdui-chip" mdui-tooltip="{content: 'API Author', position: 'top'}">
            <span class="mdui-chip-title"><i class="mdui-icon material-icons mdui-text-color-blue">account_circle</i><?= $author?></span>
        </div>
        <div class="mdui-chip" mdui-tooltip="{content: 'API Status', position: 'top'}">
            <span class="mdui-chip-title">
                <span class="status">正在获取...</span>
            </span>
        </div>
    </div>
    <div id="box">
        <h3 class="mdui-text-color-theme article-title"><i class="mdui-icon material-icons mdui-text-color-orange">view_compact</i>API 地址</h3>
        <p><?= $api_address?></p>
    </div>
    <div id="box">
            <h3 class="mdui-text-color-theme article-title"><i class="mdui-icon material-icons mdui-text-color-purple">vpn_key</i>参数列表 (打<?= $Parsedown->setBreaksEnabled(true)->line('`*`')?>是必填项)</h3>
            <p><?= $request_parameters?></p>
    </div>
    <div id="box">
            <h3 class="mdui-text-color-theme article-title"><i class="mdui-icon material-icons mdui-text-color-gray">reply</i>返回的数据</h3>
            <p><?= $return_parameters?></p>
    </div>
    <div id="footer">
        <p><?= $web['record']?></p>
        <p id="copyright"><?= "&copy;".$web['copyright']?>&nbsp;.&nbsp;Power by <a href="https://github.com/molanp">molanp</a></p>
    </div>
    <script>
        var url = window.location.origin + "/api/" + window.location.pathname.match(/\/i\/(.+)/)[1];
        $.get({
            url: url,
            complete: function(jqXHR, textStatus) {
                var status = jqXHR.status
                var content = document.getElementsByClassName("status")[0];
                if(status==200||status==301||status==302) {
                    content.innerHTML = '<i class="mdui-icon material-icons mdui-text-color-green">check</i>正常';
                } else if(status==406) {
                    content.innerHTML = '<i class="mdui-icon material-icons mdui-text-color-red">do_not_disturb</i>维护';
                } else {
                    content.innerHTML = '<i class="mdui-icon material-icons mdui-text-color-orange">warning</i>HTTP -'+status;
                }
            }
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/js/mdui.min.js"></script>
</body>
</html>