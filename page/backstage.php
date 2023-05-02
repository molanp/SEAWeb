<?php
define('IN_SYS', TRUE);
include_once('../services/Config.class.php');
include_once('../services/mark.php');
include_once('../services/until.php');
include_once('../__version__.php');

$phpversion = phpversion();
$Parsedown = new Parsedown();
$web = json_decode(curl_get('http://'.$_SERVER['HTTP_HOST'].'/info/web'),true);
$web = ($web["status"] != 200) ? die($web["data"]) : $web["data"];
if (!isset($_COOKIE['token']) || $_COOKIE['token'] != $account['password']) {
    die("<script>window.location.href='http://".$_SERVER['HTTP_HOST']."/admin'</script>");
}
$_POST = array();
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/backstage.css">
    <title><?= $web["index_web_name"];?>管理系统</title>
</head>
<body>
    <div class="navbar">
    <ul class="nav">
        <li><a href="#">首页</a></li>
        <li><a href="#">产品介绍</a></li>
        <li><a href="#">关于我们</a></li>
        <li><a href="#">您好,<strong><?= $_COOKIE['user'];?></strong></a></li>
    </ul>
    </div>

    <div class="container">
        <div class="sidebar">
            <ul class="sidebar-nav">
            <li><a href="#">导航1</a></li>
            <li><a href="#">导航2</a></li>
            <li><a href="#">导航3</a></li>
            <li><a href="#">导航4</a></li>
            <li><a href="#">导航5</a></li>
            </ul>
        </div>
        <div class="content">
            <h1>这里是内容区</h1>
            <p>这里是内容区的内容。</p>
            <?php
                echo $Parsedown->setBreaksEnabled(true)->text("> PHP版本:<strong>{$phpversion}</strong> EasyAPI_website版本:<strong>{$__version__}</strong>(最新版本)") .
                "<br/><h1>修改网页信息</h1><br>
                <form method='post' action='/login'>
                <p>标签页显示标题：<input type='text' name='index_web_name' value='{$web['index_web_name']}'></p>
                网站主页标题：<p><textarea id='editor' name='index_title'>{$web['index_title']}</textarea></p>
                网站简介信息：<p><textarea id='editor' name='index_description'>{$web['index_description']}</textarea></p>
                网站公告：<p><textarea id='editor' name='notice'>{$web['notice']['data']}</textarea></p>
                <input type='hidden' name='latesttime' value='".date('Y-m-d')."'>
                网站底部版权信息：<p><textarea id='editor' name='copyright'>{$web['copyright']}</textarea></p>
                网页备案号：<p><textarea id='editor' name='record'>{$web['record']}</textarea></p>
                友情链接(一行一个)：示例： [链接1](http://xxx)<p><textarea id='editor' name='links'>{$web['links']}</textarea></p>
                网站keywords(逗号分隔)：<p><textarea id='editor' name='keywords'>{$web['keywords']}</textarea></p>
                <p><input type='submit' value='保存'></p>
                </form>";
                ?>
        </div>
    </div>
</body>
</html>