<?php
define('IN_SYS', TRUE);
include_once('Config.class.php');
include_once('mark.php');
include_once('services/until.php');
include_once('__version__.php');

$Parsedown = new Parsedown();
$types = curl_get('http://'.$_SERVER['HTTP_HOST'].'/api');
$goto = $_SERVER["REQUEST_URI"];
preg_match('/[^\/]+[^\/]+$/', $goto, $goto);
$goto = $goto[0];

foreach(array_keys($types) as $type) {
    foreach(array_keys($types[$type]) as $p_name) {
        if ($goto == $types[$type][$p_name]['path']) {
            $api_name = $p_name;
            $data = $types[$type][$p_name];
        }
    }
}
if (!isset($data)) {
    die(json_encode($goto));
    //die(header("location: http://".$_SERVER['HTTP_HOST']));
}
$status = $data['status'];
$api_profile = $Parsedown->setBreaksEnabled(true)->line($data['api_profile']);
$version = $data['version'];
$author = $data['author'];
$api_address = $Parsedown->setBreaksEnabled(true)->text($data['api_address']);
$request_parameters = $Parsedown->setBreaksEnabled(true)->text($data['request_parameters']);
$return_parameters = $Parsedown->setBreaksEnabled(true)->text($data['return_parameters']);
$DATA = new Config($_SERVER['DOCUMENT_ROOT'].'/db/db');
$web = $DATA->get('web');
if($status=='true') {
    $status = '<strong><img src="data:image/gif;base64,R0lGODlhEAAQAMQAAE1zRW62cj6XTipfILXZt0OHRs/S0zaBO9He7nGpg0CpYfX19V15WTtmNZucm2V8ZV65dbq6uunv9U+1a1SdXovBj27ChPv7+wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAQABAAAAWW4CVKztMMzeNIYntFTAFZNFUwkRsBUOXTNUDuImH0fkAIhAJAXBwFXwVooVQIBorj8jhSrSwE5XFpWKaBWmWxICQojcuA5pv02BXKZDIoWyASCxEBBgt5ewpxDxQBFSyBBHp7AmRQfwkGRAF7ewdbRRRVAQQRkhMCDCwvABQQExSwk0I6DAcCiAIHOC4jJScpK7zCwyIhADs="title="正常">正常</font></strong>';
} elseif($status=='false') {
    $status = '<strong><img src="data:image/gif;base64,R0lGODlhEAAQAOYAAAAAAP////ngvfjXtfjPu/atnvawofezpfSgkvWklvWomvWqnPauoPydj4xNRvd/dfmRiO0YF+4bGu0bGukcHOkdHeUcHOMcHO4eHuMdHdwcHO4gH+kgIO8kI+oiIuokJOckJOEkJOonJ+8rKusqKussLN8qKustLewwMMwqKuAvL+w0NNwyMuw3N9ozM+w4OO06Ou0/P907O+5DQts8PMA1Ne5EROBCQt9CQu9KSuFISO9NTe9OTuBKSvBUVMFERONRUfFZWeRVVfFbW99UVPFdXd9WVudaWthUVPJiYvJjY+lgYPJlZfJmZuljY/NqavNra/NtbfNubvv29vv5+fv7+/X19f///wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAAFcALAAAAAAQABAAAAfGgFeCgg6FhoOIV4VIQkA9ODWFiQ5HS05KRT45NC4mDoMORElPUVBKQTszLSUhn4pGTFYCUk1DBlYvJyCfDj+iT1UHSgxVBasfGYU6PkVKEFbQCzErJB4WhTc7PkFDA1NWNjAoIhzXDjI2OTwKVTlWBCMdHxXmLDAxCVUJMQ9UBBscKJhLoWIFAgQtVqBoQECCQA28RJAocaIECREYJkQQ6MoBBw8fQnrgUIGCyWugKFRYabIlBVegHFywQNPCBUmJCBnCOSgQADs="title="维护">维护</font></strong>';
} else{
    $status = "<strong><img src='data:image/gif;base64,R0lGODlhEAAQAKIAAAAAAP///6vANwkJCPvNWvaTA7hoDv///yH5BAEAAAcALAAAAAAQABAAAAM6eLrcRzBKKV65OONKdBmZIVRWBmLi0pnoyKzXWaQvO7sN3Drld5MOHY0HEwGJlyHPYly+cE4F4chIAAA7'title='未知'>未知:$status</font></strong>";
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
    <link rel="stylesheet" href="<?php echo 'http://'.$_SERVER['HTTP_HOST']?>/assets/css/aside.css">
    <link rel="stylesheet" href="<?php echo 'http://'.$_SERVER['HTTP_HOST']?>/assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/js/mdui.min.js"></script>  
    <script src="<?php echo 'http://'.$_SERVER['HTTP_HOST']?>/assets/js/app.js"></script>  
    <title><?php echo $web["index_web_name"]?></title>
</head>
<body>
    <button onmouseover="goout(this)" onmouseout="goin(this)" id="aside_btn"> </button>
    <aside class="aside_box" id="aside">
        <center><h1><?php echo $web["index_title"]?></h1><v><?php echo 'Version '.$__version__ ?></v><hr></center>
        <ul>
            <a href="<?php echo 'http://'.$_SERVER['HTTP_HOST']?>"><li><i class="fa fa-home fa-fw"></i>&nbsp;主页</li></a>
            <a href="javascript:changeTheme()"><li>🌙&nbsp;夜间模式</li></a>
            <?php
            foreach(array_keys($types) as $type){
                echo "<v>$type</v>";
                foreach(array_keys($types[$type]) as $plugin){
                    if ($api_name == $plugin) {
                        echo "<a href='/i/".$types[$type][$plugin]["path"]."'><li id='active'>&nbsp;".$Parsedown->setBreaksEnabled(true)->line($plugin)."</li></a>";
                    } else {
                        echo "<a href='/i/".$types[$type][$plugin]["path"]."'><li>&nbsp;".$Parsedown->setBreaksEnabled(true)->line($plugin)."</li></a>";
                    }
                }
            }
            ?>
            <v>管理入口</v>
            <a href="<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/admin'?>"><li><i class="fa fa-sign-in fa-fw"></i>&nbsp;登录</li></a>
        </ul>
        <hr>
        <center><v><?php echo $web['copyright']?></v></center>

    </aside>
    <div id="title_box">
        <h1 style="text-shadow: 2px 2px 5px FFB6C1;"><?php echo $api_name?></h1>
    </div>
    <div id="box">
        <h3><i class="fa fa-star-o fa-fw"></i>&nbsp;API 简介</h3>
        <p><?php echo $api_profile?></p>
        <p><div id="badge">版本&nbsp;<?php echo $version?></div>&nbsp;&nbsp;<div id="badge">作者&nbsp;<?php echo $author?></div>&nbsp;&nbsp;<div id="badge">状态&nbsp;<?php echo $status?></div>
    </div>
    <div id="box">
        <h3><i class="fa fa-paper-plane-o fa-fw"></i>&nbsp;API 地址</h3>
        <p><?php echo $api_address?></p>
    </div>
    <div id="box">
            <h3><i class="fa fa-key fa-fw"></i>&nbsp;参数列表 (打<?php echo $Parsedown->setBreaksEnabled(true)->line('`*`')?>是必填项)</h3>
            <p><?php echo $request_parameters?></p>
    </div>
    <div id="box">
            <h3><i class="fa fa-reply fa-fw"></i>&nbsp;返回的数据</h3>
            <p><?php echo $return_parameters?></p>
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