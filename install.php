<?php
if(file_exists("configs/config.php")) {
    echo '<p>安装成功</p><p><a href="/sw-ad/">管理员登录</a></p><p><a href="/">主页</a></p>';
    die(unlink("install.php"));
}
include_once($_SERVER['DOCUMENT_ROOT']."/services/update.php");
if (isset($_POST["action"]) && $_POST["action"]=="install") {
    include_once($_SERVER['DOCUMENT_ROOT'].'/services/logger.php');
    mkdir("configs");
    mkdir("data");
    $db = $_POST['mysql_database'];
    $bind = "mysql:host={$_POST['mysql_host']};dbname={$db}";
    $username = $_POST['mysql_username'];
    $password = $_POST['mysql_password'];
    $DATABASE = new PDO($bind, $username, $password);
    $config = '<?php
    $bind = "' . ($bind ?? '') . '";
    $mysql_username = "' . ($username ?? '') . '";
    $mysql_password = "' . ($password ?? '') . '";';

    $file = fopen("configs/config.php","w");
    fwrite($file,$config);
    fclose($file);
    include_once('services/Config.class.php');
    $DATA = new Config('data/web');
    $DATA->set("web",[
        "record"=>"114514",
        "index_title"=>"SEAWeb",
        "copyright"=>"",
        "index_description"=>"这是网站简介，这里支持*MarkDown*语法",
        "notice"=>[
            "data"=>"> **这里也支持markdown语法**\n\n欢迎使用SEAWeb，本模板由[molanp](https://github.com/molanp)开发与维护。目前正在不断完善~\n如果你觉得这个API有什么不完善的地方或者说你有什么更好的想法，可以在[issues](https://github.com/molanp/easyapi_wesbite/issues)上提出建议",
            "latesttime"=>date('Y-m-d')],
        "keywords"=>"API,api",
        "links"=>"[GitHub](https://github.com/molanp/SEAWeb)\n[Issues](https://github.com/molanp/SEAWeb/issues)\n[开发指南](https://molanp.github.io/SEAWeb_docs)"
        ])->save();
    $DATA->delete("account");
    $DATA->delete("setting");
    $DATABASE->exec("CREATE TABLE IF NOT EXISTS users (
        username TEXT UNIQUE,
        password TEXT,
        token TEXT,
        apikey TEXT,
        permission INTEGER,
        regtime TEXT,
        logtime BIGINT
    )");
    
    $DATABASE->exec("DELETE FROM users");
    
    $sql = "INSERT INTO users (username, password, regtime, permission) VALUES (:username, :password, :regtime, :permission)";
    $stmt = $DATABASE->prepare($sql);
    $stmt->bindParam(':username', $_POST['usr']);
    $stmt->bindValue(':password', hash('sha256', $_POST['pwd']));
    $stmt->bindValue(':regtime', date("Y-m-d H:i:s"));
    $stmt->bindValue(':permission', 9);
    $stmt->execute();
    
    $DATABASE->exec("CREATE TABLE IF NOT EXISTS setting (
        item TEXT UNIQUE,
        value TEXT,
        info TEXT
    )");
    
    $dbData = [];
    $stmt = $DATABASE->query("SELECT item FROM setting");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dbData[] = $row['item'];
    }
    
    $itemsToAdd = array_diff(array_keys(UP_SYS), $dbData);
    $itmsToDelete = array_diff($dbData, array_keys(UP_SYS));
    
    if (!empty($itemsToAdd)) {
        $sqlAdd = "INSERT INTO setting (item, value, info) VALUES (:item, :value, :info)";
        $stmtAdd = $DATABASE->prepare($sqlAdd);
        foreach ($itemsToAdd as $item) {
            $stmtAdd->bindParam(':item', $item);
            $stmtAdd->bindValue(':value', UP_SYS[$item]['value']);
            $stmtAdd->bindValue(':info', UP_SYS[$item]['info']);
            $stmtAdd->execute();
        }
    }
    
    if (!empty($itemsToDelete)) {
        $sqlDelete = "DELETE FROM setting WHERE item = :item";
        $stmtDelete = $DATABASE->prepare($sqlDelete);
        foreach ($itemsToDelete as $item) {
            $stmtDelete->bindParam(':item', $item);
            $stmtDelete->execute();
        }
    }
    
    $DATABASE->exec("CREATE TABLE IF NOT EXISTS access_log (
        time TEXT,
        ip TEXT,
        url TEXT,
        name TEXT,
        referer TEXT,
        param TEXT
    )");
    
    $DATABASE->exec("CREATE TABLE IF NOT EXISTS api (
        id INTEGER,
        name TEXT,
        version TEXT,
        author TEXT,
        method TEXT,
        profile TEXT,
        request TEXT,
        response TEXT,
        class TEXT,
        url_path TEXT,
        file_path TEXT,
        type TEXT,
        top TEXT,
        status TEXT,
        time BIGINT
    )");
    $DATABASE->exec("CREATE INDEX idx_username ON users (username)");
    $DATABASE->exec("CREATE INDEX idx_apiname ON api (name)");
    $DATABASE->exec("CREATE INDEX idx_apiprofile ON api (profile)");
    $DATABASE->exec("CREATE INDEX idx_item ON setting (item)");
    
    echo '<p>安装成功</p><p><a href="/sw-ad/">管理员登录</a></p><p><a href="/">主页</a></p>';
    die(unlink("install.php"));
} else {
    echo '<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>安装SEAWeb</title>
    <link rel="stylesheet" href="/assets/css/mdui.css"/>
    <link rel="stylesheet" href="/assets/css/style.css"/>
    </head>
    <body class="mdui-theme-auto">
    <div style="text-align:center;" class="mdui-prose">';
    echo '<br><br>';
    echo '<mdui-card>';
    echo '<h3>欢迎使用SEAWeb</h3>';
    echo '<p>请输入以下内容来完成安装程序</p>';
    echo '<form action="install.php" method="post">
    <input type="hidden" name="action" value="install">
    <p>管理员用户名：<input type="text" name="usr" value="admin" required></p>
    <p>管理员密码：<input type="text" name="pwd" required></p>
    <p>mysql服务器地址：<input type="input" name="mysql_host" value="127.0.0.1:3306"></p>
    <p>mysql用户名：<input type="input" name="mysql_username" value="root"></p>
    <p>mysql密码：<input type="input" name="mysql_password"></p>
    <p>mysql数据库名：<input type="input" name="mysql_database"></p>
    <input type="submit">
    </form>';
    echo '</mdui-card></div>';
    echo '<script src="https://unpkg.com/mdui@2.0.1/mdui.global.js"></script>';
}
?>