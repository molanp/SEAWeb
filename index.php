<?php
define('IN_SYS', TRUE);
include_once($_SERVER['DOCUMENT_ROOT'].'/Config.class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/mark.php');
$Parsedown = new Parsedown();
$DATA = new Config($_SERVER['DOCUMENT_ROOT'].'/db/db');
if (!file_exists('db/db.php')) {
    $DATA->set("account",["username"=>"admin","password"=>md5('password')])->save();
    $DATA->set("web",["record"=>"","index_web_name"=>"Easy_API","index_title"=>"欢迎使用本模板！这是默认的标题","copyright"=>"All copyright molanp","index_description"=>"这是网站简介","notice"=>["data"=>"欢迎使用 Easy API模板，本模板由molanp开发与维护。目前正在不断完善~</br>如果你觉得这个API有什么不完善的地方或者说你有什么更好的想♂法，可以在<a href='https://github.com/molanp/easyapi_wesbite/issues'>issues</a>上提出建议","latesttime"=>date('Y-m-d')],"keywords"=>"API,api"])->save();
}
$web = $DATA->get('web');
$aside_list = json_decode(file_get_contents('http://'.$_SERVER['HTTP_HOST'].'/api'),true);
$_version=fopen('__version__',"r");
$version = fread($_version,filesize("__version__"));
$version = explode(':',$version)[1];
fclose($_version);
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
    <link rel="Shortcut Icon" href="assets/img/favicon.ico">
    <link rel="bookmark" href="assets/img/favicon.ico" type="image/x-icon" /> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/css/mdui.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha512-SfTiTlX6kk+qitfevl/7LibUOeJWlt9rbyDn92a1DqWOw9vWG2MFoays0sgObmWazO5BQPiFucnnEAjpAB+/Sw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="assets/css/aside.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/js/mdui.min.js"></script>  
    <title><?php echo $web["index_web_name"]?></title>
</head>
<body>
    <button onmouseover="goout(this)" onmouseout="goin(this)" id="aside_btn"> </button>
    <aside class="aside_box" id="aside">
        <center><h1><?php echo $web["index_title"]?></h1><v><?php echo 'Version '.$version ?></v><hr></center>
        <ul>
            <a href="#"><li id="active"><i class="fa fa-home fa-fw"></i>&nbsp;主页</li></a>
            <a href="javascript:changeTheme()"><li>🌙&nbsp;夜间模式</li></a>
            <?php
            foreach(array_keys($aside_list) as $type){
                echo "<v>$type</v>";
                foreach(array_keys($aside_list[$type]) as $plugin){
                    echo "<a href='home?".$aside_list[$type][$plugin]["path"]."'><li>&nbsp;".$Parsedown->setBreaksEnabled(true)->line($plugin)."</li></a>";
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
        <p><?php echo $web['index_description']?></p>
    </div>
    <div id="box">
        <h3><i class="fa fa-paper-plane-o fa-fw"></i>&nbsp;公告<?php echo $Parsedown->setBreaksEnabled(true)->line('`'.$web['notice']['latesttime'].'`');?></h3>
        <p><?php echo $web['notice']['data']?></p>
    </div>
    <div id="box">
            <h3><i class="fa fa-link fa-fw"></i>&nbsp;友情链接</h3>
            <p>1wssssssssssssss
                ssssssssssssssssssssssssseeeeeeeee
                eeeeeeeeeevahdnnnnnnnnnnnnnnnnnnnnnnnaaaaaaaaaa
                aaaaaaaaaaaaaaaaaa dssssssssssssssssssssssssjs
                hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhja</p>
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

    let darkMode = localStorage.getItem("theme");
    if (darkMode === "dark") enableDarkMode();
    function enableDarkMode() {
        document.body.classList.add("dark")
        localStorage.setItem("theme", "dark")
    };
    function disableDarkMode() {
        document.body.classList.remove("dark")
        localStorage.setItem("theme", "light")
    };
    function changeTheme() {
        darkMode = localStorage.getItem("theme")
        if (darkMode === "dark") {
            disableDarkMode()
        } else {
            enableDarkMode()
        }
    };
    window
  .matchMedia("(prefers-color-scheme: dark)")
  .addListener(e => (e.matches ? enableDarkMode() : disableDarkMode()))
  function goout(x) {
    x.style.backgroundColor='#eb6161';
    x.style.marginLeft="-5px"
  }
  function goin(x) {
    x.style.backgroundColor='#F08080';
    x.style.marginLeft="-20px"
  }
  a=document.getElementById("aside_btn");
  a.style.marginLeft="-20px";
</script>
</html>