<?php
if(file_exists("configs")) {
    echo '<p>安装成功</p><p><a href="/sw-ad/">管理员登录</a></p><p><a href="/">主页</a></p>';
    die(unlink("install.php"));
}
include_once($_SERVER['DOCUMENT_ROOT']."/services/update.php");
if (isset($_POST["action"]) && $_POST["action"]=="install") {
    mkdir("configs");
    mkdir("data");
    if (isset($_POST['sqlite']) && $_POST["sqlite"]=="y") {
        $mode = 'true';
        define('DATABASE', new PDO("sqlite:".$_SERVER["DOCUMENT_ROOT"]."/data/main.db"));
    } else {
        $mode = 'false';
        $bind = "mysql:host={$_POST['mysql_host']};dbname={$_POST['$mysql_database']}";
        $username = $_POST['mysql_username'];
        $password = $_POST['mysql_password'];
        define('DATABASE', new PDO($bind, $username, $password));
    }
    $config = '<?php
    $sqlite_mode = ' .$mode. ';
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
    //数据迁移
    $DATA->delete("account");
    $DATA->delete("setting");

    DATABASE->exec("CREATE TABLE IF NOT EXISTS users(username TEXT, password TEXT, token TEXT, apikey TEXT, permission INTEGER, regtime TEXT, logtime BIGINT)");

    $sql = "INSERT INTO users (username, password, regtime, permission) VALUES (:username, :password, :regtime, :permission)";
    $stmt = DATABASE->prepare($sql);
    $stmt->bindParam(':username', $_POST['usr']);
    $stmt->bindValue(':password', hash('sha256', $_POST['pwd']));
    $stmt->bindValue(':regtime', date("Y-m-d H:i:s"));
    $stmt->bindValue(':permission', 9);
    $stmt->execute();
    
    DATABASE->exec("CREATE TABLE IF NOT EXISTS setting(item TEXT, value TEXT, info TEXT)");
    $dbData = [];
    $stmt = DATABASE->query("SELECT item FROM setting");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dbData[] = $row['item'];
    }
    $itemsToAdd = array_diff(array_keys(UP_SYS), $dbData);
    $itmsToDelete = array_diff($dbData, array_keys(UP_SYS));

    if (!empty($itemsToAdd)) {
        $sqlAdd = "INSERT INTO setting (item, value, info) VALUES (:item, :value, :info)";
        $stmtAdd = DATABASE->prepare($sqlAdd);
        foreach ($itemsToAdd as $item) {
            $stmtAdd->bindParam(':item', $item);
            $stmtAdd->bindValue(':value', UP_SYS[$item]['value']);
            $stmtAdd->bindValue(':info', UP_SYS[$item]['info']);
            $stmtAdd->execute();
        }
    }

    if (!empty($itemsToDelete)) {
        $sqlDelete = "DELETE FROM setting WHERE item = :item";
        $stmtDelete = DATABASE->prepare($sqlDelete);
        foreach ($itemsToDelete as $item) {
            $stmtDelete->bindParam(':item', $item);
            $stmtDelete->execute();
        }
    }
    DATABASE->exec("CREATE TABLE IF NOT EXISTS api(id INTEGER, name TEXT, version TEXT, author TEXT, method TEXT, profile TEXT, request TEXT, response TEXT, class TEXT, url_path TEXT, file_path TEXT, type TEXT, top TEXT, status TEXT, time BIGINT, PRIMARY KEY (name, type))");
    DATABASE->exec("CREATE TABLE IF NOT EXISTS access_log(time TEXT, ip TEXT, url TEXT, name TEXT, referer TEXT, param TEXT)");
    echo '<p>安装成功</p><p><a href="/sw-ad/">管理员登录</a></p><p><a href="/">主页</a></p>';
    unlink("install.php");
} else {
    echo '<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>安装SEAWeb</title>';
    echo '<h1>欢迎使用SEAWeb</h1>';
    echo '<p>项目使用GPL3许可证</p>';
    echo '<form action="install.php" method="post">
    <input type="hidden" name="action" value="install">
    <p>管理员用户名：<input type="text" name="usr" value="admin" required></p>
    <p>管理员密码：<input type="text" name="pwd" required></p>
    <br/>
    <p><input type="checkbox" id="scales" name="sqlite" value="y"/>
    <label for="scales">是否使用本地数据存储？</label></p>
    <p>若上述为true，下面的内容将被忽略</p>
    <p>mysql服务器地址：<input type="input" name="mysql_host" value="127.0.0.1:3306"></p>
    <p>mysql用户名：<input type="input" name="mysql_username" value="root"></p>
    <p>mysql密码：<input type="input" name="mysql_password"></p>
    <p>mysql数据库名：<input type="input" name="mysql_database"></p>
    <input type="submit">
    </form>';
}
?>