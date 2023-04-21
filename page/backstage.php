<?php
define('IN_SYS', TRUE);
include_once('../services/Config.class.php');
include_once('../services/mark.php');
include_once('../__version__.php');

$phpversion = phpversion();
$Parsedown = new Parsedown();
$web = $DATA->get('web');
if (!isset($_COOKIE['token']) || $_COOKIE['token'] != $account['password']) {
    die("<script>window.location.href='http://".$_SERVER['HTTP_HOST']."/admin'</script>");
}
$_POST = array();
?>
 <!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title><?php echo $web["index_web_name"]?>网站管理</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.staticfile.org/jquery/1.10.2/jquery.min.js"></script>
    <link rel="stylesheet" href="../assets/css/backstage.css">
</head>
<style>
    /*********** 声明公共元素样式 ***********/
* {
    margin: 0;
    padding: 0;
}
body {
    background-color: #efefef;
}
li {
    list-style-type: none;
}
li a {
    color:#000;
    text-decoration-line: none;
}
a:hover {
    color: brown;
    text-decoration-line: underline;
}
#footer {
    position: fixed;
    bottom: 0;
    width: 100%;
    height: 60px;
    line-height: 10px;
    text-align: center;
    box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
    background-color: #ffffff20;
    backdrop-filter: blur(20px);
}
/*********** 声明顶部样式 ***********/
header {
    background: linear-gradient(to top, lightgrey, #efefef);
    margin: 10px 20px;
    overflow: hidden;
    height: 20%;
    border-bottom-left-radius: 20px;
    border-bottom-right-radius: 6px;}
header div {
    width: 100%;
    margin: auto;
}
header h1 {
    float: left;
    margin-left: 20px;
    font-weight: normal;
}
header nav {
    float: right;
    margin-right: 20px;
}
header nav ul li {
    float: left;
    padding-left: 30px;
    line-height: 80px;
}
/*********** 声明主体区样式 ***********/
/*侧边导航栏*/
main {
    width: 80%;  /*内容区宽度*/
    height: 80%;
    padding-left: 15%;
    overflow: hidden;
    /*布局参考线*/
    /*border: 1px solid red;*/
}
main article {
    float: left;
    /*布局参考色块*/
    /*background-color: #FD6FCF;*/
    width: 100%;
    min-height: 100%;
    background: linear-gradient(to bottom, lightgrey, #ededed);
    box-shadow: inset 0 1px 0 rgba(37, 207, 240, 0.1);
    border-radius: 6px;
}
main aside {
    float: left;
    border-radius: 6px;
    background:linear-gradient(to left, lightgrey, #ededed);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1);
    width: 10%;
    margin-left: -100%;
    position: relative;
    left: -15%;
}
main aside nav li {
    line-height: 2rem;
}
main aside nav li:first-child,main aside nav li a{
    padding: 10px 15px;
    display: block;
}
main aside nav li a.active,main aside nav li a:hover {
    border-left: 3px solid brown!important;
    background: #efefef;
    padding-left: 15px;
    margin-left: -3px;
}
main article iframe {
    min-width: 100%;
    min-height: 700px;
    margin: auto;
    border: none;
}

/*弹窗*/
.dialog {
	display: none;
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	overflow: auto;
	background-color: rgba(0, 0, 0, 0.4);
}

.content {
	width: 500px;
	height: 300px;
	margin: 100px auto;
	background-color: #fefefe;
	border-radius: 10px;
	box-shadow: 0 0 5px 5px darkgray;
}
.aclose{
	width: 500px;
	height: 60px;
	text-align: center;
}
.aclose span{
	line-height: 70px;
	font-size: 26px;
	font-weight: 700;
}
.contain{
	width: 500px;
	height: 230px;
	font-size: 20px;
	margin-top: 10px;
	text-align: center;
}

.close {
	color: #aaa;
	float: right;
	margin-right: 15px;
	font-size: 40px;
	font-weight: bold;
	text-decoration: none;
}

.inputText input
{
    border: 0;	/* 删除输入框边框 */
    padding: 10px 10px;	/* 输入框内的间距 */
    border-bottom: 1px solid black;	/* 输入框白色下划线 */
    background-color: #00000000;	/* 输入框透明 */
    color: black;	/* 输入字体的颜色 */
}
</style>
<body>
<!--顶部信息区-->
<header role="header">
    <div>
        <h1><?php echo $web["index_title"]?>网站管理系统</h1>
        <nav role="user">
            <ul>
                <li>您好,<strong><?php echo $_COOKIE['user'];?></strong></li>
                <li><a onclick="show()" target="main">修改密码</a></li>
                <li><form method="post"><input type="submit" name="logout" value="登出"></from></li>
            </ul>
        </nav>
    </div>
</header>
<!--圣杯二列布局-->
<main role="main">
    <!--主体内联框架区-->
    <article role="content">
        <?php
        echo $Parsedown->setBreaksEnabled(true)->text("> PHP版本:<strong>$phpversion</strong> EasyAPI_website版本:<strong>$__version__</strong>(最新版本)");
        echo "<br/><h1>修改网页信息</h1><br>
        <form method='post'>
        <p>标签页显示标题：<input type='text' name='index_web_name' value='".$web["index_web_name"]."'></p>
        网站主页标题：<p><textarea name='index_title'>".$web["index_title"]."</textarea></p>
        网站简介信息：<p><textarea name='index_description'>".$web["index_description"]."</textarea></p>
        网站公告：<p><textarea name='notice'>".$web["notice"]["data"]."</textarea></p>
        <input type='hidden' name='latesttime' value='".date('Y-m-d')."'>
        网站底部版权信息：<p><textarea name='copyright'>".$web["copyright"]."</textarea></p>
        网页备案号：<p><textarea name='record'>".$web["record"]."</textarea></p>
        友情链接(一行一个)：示例： [链接1](http://xxx)<p><textarea name='links'>".$web["links"]."</textarea></p>
        网站keywords(逗号分隔)：<p><textarea name='keywords'>".$web["keywords"]."</textarea></p>
        <p><input type='submit' value='保存'></p>
        </form>";
?>
    </article>
    <!--左侧导航区-->
    <aside>
        <nav role="option">
            <ul>
                <li><a href="#" target="main"  class="active" >主页</a></li>
                <!--<li><a href="user.html" target="main">用户管理</a></li>
                <li><a href="article.html" target="main">文档管理</a></li>
                <li><a href="category.html" target="main">留言管理</a></li>
                <li><a href="product.html" target="main">产品管理</a></li>-->
            </ul>
        </nav>
    </aside>
</main>
<div class="dialog">
	<div class="content">
		<div class="aclose">
			<span>修改密码</span>
			<a class="close" href="javascript:close();">&times;</a>
		</div>
		<div class="contain">
            <p style="color:red">注意，密码中请不要包含/^[/s]+$/等特殊字符！</p>
            <br/>
            <div class="inputText">
                <form method="post">
                    新密码<input type="password" name="newpassword"/><br/>
                    再次输入新密码<input type="password" name="repassword"/>
                    <br/>
                    <br/>
                    <input type="submit" value="确认修改"> 
                </form>
            </div>
		</div>
	</div>	
</div>
</body>
<script>
	function show(){
		var show = $(".dialog").css("display");
		$(".dialog").css("display",show =="none"?"block":"none");
	}
	function close(){
		var show = $(".dialog").css("display");
		$(".dialog").css("display",show =="none"?"block":"none");
	}
</script>
</html>
<?php
    if (isset($_POST["newpassword"]) && isset($_POST["repassword"]) && !empty($_POST))
        {
        if (!preg_match("/^[a-zA-Z0-9]*$/",$_POST["newpassword"]))
            {
                echo '<script>alert("只允许字母和数字");</script>'; 
            }else if (empty($_POST["repassword"]))
            {
                echo '<script>alert("密码不得为空");</script>';
            } else if(!preg_match("/^[a-zA-Z0-9]*$/",$_POST["repassword"]))
                {
                    echo '<script>alert("只允许字母和数字");</script>'; 
                }else if($_POST['newpassword']!=$_POST['repassword']) {
                    echo '<script>alert("两次密码输入不一致，请重新输入！");</script>'; 
                }else if($_POST['newpassword']==$_POST['repassword'] && isset($_POST["newpassword"])) {
                    $DATA->set("account",["username"=> $_COOKIE['user'],"password"=>base64_encode($_POST['newpassword'])])->save();
                    //die("<script>alert('修改成功，请重新登录！');window.location.reload();</script>");
                }
            }
?>