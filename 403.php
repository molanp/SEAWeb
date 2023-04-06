<!DOCTYPE html>
<html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">  
    <meta http-equiv="X-UA-Compatible" content="IE=edge">  
    <meta name="viewport" content="width=device-width, initial-scale=1">  
  
    <title>403 Forbidden</title>  
      
    <style type="text/css">  
    body {  
        background-color: #0099CC;  
        color: #FFFFFF;  
        font-family: Microsoft Yahei, "Helvetica Neue", Helvetica, Hiragino Sans GB, WenQuanYi Micro Hei, sans-serif;  
        margin-left: 100px;  
    }  
    .face {  
        font-size: 100px;  
    }  
    p{  
        font-size: 24px;  
        padding: 8px;  
        line-height: 40px;  
    }  
    .tips {  
        font-size: 16px  
    }  
      
    /*针对小屏幕的优化*/  
    @media screen and (max-width: 600px) {   
        body{  
            margin: 0 10px;  
        }  
        p{  
            font-size: 18px;  
            line-height: 30px;  
        }  
        .tips {  
            display: inline-block;  
            padding-top: 10px;  
            font-size: 14px;  
            line-height: 20px;  
        }  
    }  
    </style>  
</head>  
  
<body>  
    <script>   
    var i = 5;  //这里是倒计时的秒数  
    var intervalid;   
    intervalid = setInterval("cutdown()", 1000);   
    function cutdown() {   
        if (i == 0) {   
            window.location.href = "<?php echo 'http://'.$_SERVER['HTTP_HOST']?>"; //倒计时完成后跳转的地址  
            clearInterval(intervalid);   
        }   
        document.getElementById("mes").innerHTML = i;   
        i--;   
    }  
    window.onload = cutdown;  
    </script>  
      
    <span class="face">:(</span>  
    <p>403 Forbidden<br>  
    <p>You don't have permission to access this resource.</p>
        <span id="mes"></span> 秒后转至主页，您可以在那里试着找找您所需要的信息。<br>  
        <span class="tips">如果您想了解更多信息，则可以稍后在线搜索此错误: Forbidden: Access is denied.
    </span>  
    </p>  
</body>  
</html>  