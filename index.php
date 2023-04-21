<?php
define('IN_SYS', TRUE);
include_once('services/Config.class.php');
include_once('services/mark.php');
include_once('services/until.php');
include_once('__version__.php');
$DATA = new Config($_SERVER['DOCUMENT_ROOT'].'/db/db');
if(!file_exists('db')) {
    mkdir('db');
    $DATA->set("account",["username"=>"admin","password"=>base64_encode('password')])->save();
    $DATA->set("web",[
        "record"=>"",
        "index_web_name"=>"Easy_API",
        "index_title"=>"欢迎使用本模板！这是默认的标题",
        "copyright"=>"All copyright molanp",
        "index_description"=>"这是网站简介，这里支持*MarkDown*语法",
        "notice"=>[
            "data"=>"> **这里也支持markdown语法**\n欢迎使用SEAWeb，本模板由[molanp](https://github.com/molanp)开发与维护。目前正在不断完善~\n如果你觉得这个API有什么不完善的地方或者说你有什么更好的想♂法，可以在[issues](https://github.com/molanp/easyapi_wesbite/issues)上提出建议",
            "latesttime"=>date('Y-m-d')],
        "keywords"=>"API,api",
        "links"=>"[GitHub](https://github.com/molanp/SEAWeb)\n[Issues](https://github.com/molanp/SEAWeb/issues)\n[开发指南](https://molanp.github.io/easyapi_website/)"])->save();
}
$Parsedown = new Parsedown();
$web = $DATA->get('web');
$aside_list = curl_get('http://'.$_SERVER['HTTP_HOST'].'/api');
if ($aside_list["data"]["status"]!=200) {
    die($aside_list["data"]["data"]);
} else {
    $aside_list = $aside_list["data"]["data"];
}
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
    <meta name="keywords" content="<?php echo $web["keywords"];?>">
    <link rel="stylesheet" href="https://font.sec.miui.com/font/css?family=MiSans:400,500,600,700:Chinese_Simplify,Latin,Chinese_Traditional&amp;display=swap">
    <link rel="Shortcut Icon" href="<?php echo 'http://'.$_SERVER['HTTP_HOST']?>/favicon.ico">
    <link rel="bookmark" href="<?php echo 'http://'.$_SERVER['HTTP_HOST']?>/favicon.ico" type="image/x-icon" /> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/css/mdui.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha512-SfTiTlX6kk+qitfevl/7LibUOeJWlt9rbyDn92a1DqWOw9vWG2MFoays0sgObmWazO5BQPiFucnnEAjpAB+/Sw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="assets/css/aside.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/js/mdui.min.js"></script>
    <script src="assets/js/app.js"></script>  
    <title><?php echo $web["index_web_name"]?></title>
</head>
<body>
    <button onmouseover="goout(this)" onmouseout="goin(this)" id="aside_btn"> </button>
    <aside class="aside_box" id="aside">
        <center><h1><?php echo $web["index_title"]?></h1><v><?php echo 'Version '.$__version__ ?></v><hr></center>
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
        <center><v>&copy;<?php echo $web['copyright']?></v></center>

    </aside>
    <div id="title_box">
        <h1 style="text-shadow: 2px 2px 5px FFB6C1;"><?php echo $web['index_title']?></h1>
    </div>
    <div id="box">
        <h3><i class="fa fa-star-o fa-fw"></i>&nbsp;网站简介</h3>
        <p><?php echo $Parsedown->setBreaksEnabled(true)->text($web['index_description'])?></p>
    </div>
    <div id="box">
        <h3><i class="fa fa-paper-plane-o fa-fw"></i>&nbsp;公告<?php echo $Parsedown->setBreaksEnabled(true)->line('`'.$web['notice']['latesttime'].'`');?></h3>
        <p><?php echo $Parsedown->setBreaksEnabled(true)->text($web['notice']['data'])?></p>
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
        <p><?php echo $web['record']?></p>
        <p id="copyright"><?php echo "&copy;".$web['copyright']?>&nbsp;.&nbsp;Power by <a href="https://github.com/molanp">molanp</a></p>
    </div>
</body>
<script>
    var box = document.getElementById("aside")
    var btn = document.getElementById("aside_btn")
    btn.onclick = function() {
        if (box.offsetLeft == 0) {
            box.style['margin-left'] = -1*document.body.clientWidth + "px"
        } else {
            box.style['margin-left'] = 0 + "px"
        }
    }
    btn.style.marginLeft="-20px";
    if (darkMode === "dark") enableDarkMode();
</script>
</html>