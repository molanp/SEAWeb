<?php
define('IN_SYS', TRUE);
include_once('../services/Config.class.php');
//include_once('../services/mark.php');
include_once('../services/until.php');
include_once('../__version__.php');

$phpversion = phpversion();
//$Parsedown = new Parsedown();
$web = curl_get('http://'.$_SERVER['HTTP_HOST'].'/v2/info',["for"=>"web"]);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/css/mdui.min.css" />
    <link rel="stylesheet" href="/assets/css/mark.css">
    <link rel="stylesheet" href="/assets/css/backstage.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/js/mdui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.17/sweetalert2.all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="/assets/js/login.js"></script>
    <title><?= $web["index_title"];?>管理系统</title>
</head>
<body>
    <div class="navbar">
    <ul class="nav">
        <li><a href="#">首页</a></li>
        <li><a href="javascript:loginout()">退出登录</a></li>
        <li><a href="javascript:resetpassword()">修改密码</a></li>
        <li><a href="#">您好,<strong><?= $_COOKIE['user'];?></strong></a></li>
    </ul>
    </div>

    <div class="container">
        <div class="sidebar">
            <ul class="sidebar-nav">
            <li><a href="#" id="avtive">导航1</a></li>
            <li><a href="#">导航2</a></li>
            <li><a href="#">导航3</a></li>
            <li><a href="#">导航4</a></li>
            <li><a href="#">导航5</a></li>
            </ul>
        </div>
        <div class="content">
            <?php
                echo "<h3>系统信息</h3>
                <blockquote>
                PHP版本:{$phpversion}
                <br>
                SEAWeb版本:{$__version__}(最新版本)
                </blockquote>
                <br>
                <h3>修改网页信息</h3>
                <br>
                网站标题：<p><textarea id='editor' name='index_title'>{$web['index_title']}</textarea></p>
                网站简介信息：<p><textarea id='editor' name='index_description'>{$web['index_description']}</textarea></p>
                网站公告：<p><textarea id='editor' name='notice'>{$web['notice']['data']}</textarea></p>
                网站底部版权信息：<p><textarea id='editor' name='copyright'>{$web['copyright']}</textarea></p>
                网页备案号：<p><textarea id='editor' name='record'>{$web['record']}</textarea></p>
                友情链接(一行一个)：示例： [链接1](http://xxx)<p><textarea id='editor' name='links'>{$web['links']}</textarea></p>
                网站keywords(逗号分隔)：<p><textarea id='editor' name='keywords'>{$web['keywords']}</textarea></p>
                <button onclick='save()'
                class='login-button mdui-btn mdui-btn-raised mdui-ripple'>保存</button>";
                ?>
        </div>
    </div>
    <script>
        function save() {
            var send = {
                'for':'edit',
                'token':getCookie('token'),
                'record':document.getElementsByName("record")[0].value,
                'index_title':document.getElementsByName("index_title")[0].value,
                'copyright':document.getElementsByName("copyright")[0].value,
                'index_description':document.getElementsByName("index_description")[0].value,
                'notice':document.getElementsByName("notice")[0].value,
                'keywords':document.getElementsByName("keywords")[0].value,
                'links':document.getElementsByName("links")[0].value
            };
            sendData("/v2/info", send, function(data, status) {
                if (status === 'success') {
                    if (data.status == 200) {
                        regsuc(data.data);
                    } else {
                        regFail(data.data);
                    }
                } else {
                    regFail("连接服务器失败");
                }
        });
    }
    </script>
</body>

</html>