<?php
define('IN_SYS', TRUE);
include_once('services/Config.class.php');
include_once('services/mark.php');
include_once('services/markExtra.php');
include_once('services/until.php');

load();
$Parsedown = new ParsedownExtra();
$web = curl_get('http://'.$_SERVER['HTTP_HOST'].'/v2/info',["for"=>"web"]);
$aside_list = curl_get('http://'.$_SERVER['HTTP_HOST'].'/v2/info');
$web = ($web["status"] != 200) ? die($web["data"]) : $web["data"];
$aside_list = ($aside_list["status"] != 200) ? die($aside_list["data"]) : $aside_list["data"];
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/css/mdui.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha512-SfTiTlX6kk+qitfevl/7LibUOeJWlt9rbyDn92a1DqWOw9vWG2MFoays0sgObmWazO5BQPiFucnnEAjpAB+/Sw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/assets/css/aside.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/mark.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/js/mdui.min.js"></script>
    <script src="/assets/js/app.js"></script>  
    <title><?= $web["index_title"]?></title>
</head>
<body>
    <button onmouseover="goout(this)" onmouseout="goin(this)" id="aside_btn"> </button>
    <aside class="aside_box" id="aside">
        <center><h1><?= $web["index_title"]?></h1><v><?= 'Version '.$web["version"] ?></v><hr></center>
        <ul>
            <a href="#"><li id="active"><i class="fa fa-home fa-fw"></i>&nbsp;主页</li></a>
            <a href="javascript:changeTheme()"><li>🌙&nbsp;夜间模式</li></a>
            <?php
            foreach(array_keys($aside_list) as $type){
                echo "<v>$type</v>";
                foreach(array_keys($aside_list[$type]) as $plugin){
                    echo "<a href='/i/".$aside_list[$type][$plugin]["path"]."'><li>&nbsp;".$plugin."</li></a>";
                }
            }
            ?>
            <v>管理入口</v>
            <a href="admin"><li><i class="fa fa-sign-in fa-fw"></i>&nbsp;登录</li></a>
        </ul>
        <hr>
        <center><v>&copy;<?= $web['copyright']?></v></center>

    </aside>
    <div id="title_box">
        <h1 style="text-shadow: 2px 2px 5px FFB6C1;"><?= $web['index_title']?></h1>
    </div>
    <div id="box">
        <h3><i class="fa fa-star-o fa-fw"></i>&nbsp;网站简介</h3>
        <p><?= $Parsedown->setBreaksEnabled(true)->text($web['index_description'])?></p>
    </div>
    <div id="box">
        <h3><i class="fa fa-paper-plane-o fa-fw"></i>&nbsp;公告<?= $Parsedown->setBreaksEnabled(true)->line('`'.$web['notice']['latesttime'].'`');?></h3>
        <p><?= $Parsedown->setBreaksEnabled(true)->text($web['notice']['data'])?></p>
    </div>
    <div id="box">
            <h3><i class="fa fa-link fa-fw"></i>&nbsp;友情链接</h3>
            <p><?php
            if(!empty($web['links'])){
                $links = preg_split("/\n/", $Parsedown->setBreaksEnabled(true)->line($web['links']));
                for($i = 0; $i < count($links); $i++) {
                    echo '<span id="badge">🤣'.$links[$i].'</span>';
                }
            }?></p>
    </div>
    <div id="footer">
        <p><?= $web['record']?></p>
        <p id="copyright"><?= "&copy;".$web['copyright']?>&nbsp;.&nbsp;Power by <a href="https://github.com/molanp">molanp</a></p>
    </div>
</body>
</html>