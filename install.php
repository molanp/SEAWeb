<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>安装SEAWeb</title>
    <link rel="stylesheet" href="https://unpkg.com/mdui@2.0.3/mdui.css" />
    <link rel="stylesheet" href="/assets/css/style.css" />
</head>

<body class="mdui-theme-auto">
<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/__version__.php");
if (file_exists("configs/config.php")) {
    echo "<p>安装成功</p><p><a href="/sw-ad/">管理员登录</a></p><p><a href="/">主页</a></p>";
    die(unlink("install.php"));
}
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/update.php");
if (isset($_POST["action"]) && $_POST["action"] == "install") {
    try {
        mkdir("configs");
        mkdir("data");
        $db = $_POST["mysql_database"];
        $bind = "mysql:host={$_POST["mysql_host"]};dbname={$db}";
        $username = $_POST["mysql_username"];
        $password = $_POST["mysql_password"];
        $DATABASE = new PDO($bind, $username, $password);
        $config = "<?php
        $bind = "" . ($bind ?? "") . "";
        $mysql_username = "" . ($username ?? "") . "";
        $mysql_password = "" . ($password ?? "") . "";";

        $file = fopen("configs/config.php", "w");
        fwrite($file, $config);
        fclose($file);
        include_once("services/Config.class.php");
        $DATA = new Config("data/web");
        $DATA->set("web", [
            "record" => "",
            "index_title" => "SEAWeb",
            "copyright" => "molanp 2023",
            "index_description" => "这是网站简介，这里支持*MarkDown*语法",
            "notice" => [
                "data" => "> **这里也支持markdown语法**\n\n欢迎使用SEAWeb，本模板由[molanp](https://github.com/molanp)开发与维护。目前正在不断完善~\n如果你觉得这个API有什么不完善的地方或者说你有什么更好的想法，可以在[issues](https://github.com/molanp/SEAWeb/issues)上提出建议",
                "latesttime" => date("Y-m-d")
            ],
            "keywords" => "API,api",
            "links" => "[GitHub](https://github.com/molanp/SEAWeb)\n[Issues](https://github.com/molanp/SEAWeb/issues)\n[开发指南](https://molanp.github.io/SEAWeb_docs)"
        ])->save();
        $DATA->delete("account");
        $DATA->delete("setting");
        $DATABASE->exec("CREATE TABLE IF NOT EXISTS users (
            username TEXT,
            password TEXT,
            token TEXT,
            apikey TEXT,
            permission INTEGER,
            regtime TEXT,
            logtime BIGINT,
            INDEX idx_username (username(50))
        )");

        $sql = "REPLACE INTO users (username, password, regtime, permission) VALUES (:username, :password, :regtime, :permission)";
        $stmt = $DATABASE->prepare($sql);
        $stmt->bindParam(":username", $_POST["usr"]);
        $stmt->bindValue(":password", hash("sha256", $_POST["pwd"]));
        $stmt->bindValue(":regtime", date("Y-m-d H:i:s"));
        $stmt->bindValue(":permission", 9);
        $stmt->execute();

        $DATABASE->exec("CREATE TABLE IF NOT EXISTS setting (
            item TEXT,
            value TEXT,
            info TEXT,
            INDEX idx_setting (item(50))
        )");

        $dbData = [];
        $stmt = $DATABASE->query("SELECT item FROM setting");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dbData[] = $row["item"];
        }

        $itemsToAdd = array_diff(array_keys(UP_SYS), $dbData);
        $itmsToDelete = array_diff($dbData, array_keys(UP_SYS));

        if (!empty($itemsToAdd)) {
            $sqlAdd = "REPLACE INTO setting (item, value, info) VALUES (:item, :value, :info)";
            $stmtAdd = $DATABASE->prepare($sqlAdd);
            foreach ($itemsToAdd as $item) {
                $stmtAdd->bindParam(":item", $item);
                $stmtAdd->bindValue(":value", UP_SYS[$item]["value"]);
                $stmtAdd->bindValue(":info", UP_SYS[$item]["info"]);
                $stmtAdd->execute();
            }
        }

        if (!empty($itemsToDelete)) {
            $sqlDelete = "DELETE FROM setting WHERE item = :item";
            $stmtDelete = $DATABASE->prepare($sqlDelete);
            foreach ($itemsToDelete as $item) {
                $stmtDelete->bindParam(":item", $item);
                $stmtDelete->execute();
            }
        }

        $DATABASE->exec("CREATE TABLE IF NOT EXISTS access_log (
            time TEXT,
            ip TEXT,
            url TEXT,
            name TEXT,
            referer TEXT,
            param TEXT,
            INDEX idx_access (time(50), ip(50), url(50), name(50), referer(50))
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
            time BIGINT,
            INDEX idx_api (name(50), profile(50))
        )");?>
        <div class="container">
            <div class="item"></div>
            <mdui-card class="item">
                <h3>安装成功</h3>
                <mdui-button href="/">主页</mdui-button>
                <mdui-button href="/sw-ad">管理员登录</mdui-button>
            </mdui-card>
            <div class="item"></div>
        </div>
        <?php
        @unlink("install.php");
    } catch (\Exception $exception) {
        @unlink("data/web.php");
        @unlink("configs/config.php");
        @rmdir("data");
        @rmdir("configs");
        ?>
        <div class="container">
            <div class="item"></div>
            <mdui-card class="item">
                <h3>安装失败</h3>
                <mdui-button href="/">重试</mdui-button>
                <p>原因如下</p>
                <mdui-text-field autosize readonly value="<?=$exception?>"></mdui-text-field>
            </mdui-card>
            <div class="item"></div>
        </div>
<?php }
} else {?>
    <div class="container">
        <div class="item"></div>
        <mdui-card class="item">
            <h3>欢迎使用SEAWeb</h3>
            <p>你正在安装<b>SEAWeb <?= $__version__?></b></p>
            <p>请输入以下内容来完成安装程序</p>
            <form action="install.php" method="post">
                <mdui-text-field type="hidden" name="action" value="install"></mdui-text-field>
                <mdui-text-field name="usr" helper="管理员用户名" value="admin"></mdui-text-field>
                <mdui-text-field name="pwd" helper="管理员密码" placeholder="password"></mdui-text-field>
                <mdui-text-field name="mysql_host" helper="MYSQL服务器地址" value="127.0.0.1:3306"></mdui-text-field>
                <mdui-text-field name="mysql_username" helper="MYSQL用户名" value="root"></mdui-text-field>
                <mdui-text-field name="mysql_password" helper="MYSQL密码" placeholder="password"></mdui-text-field>
                <mdui-text-field name="mysql_database" helper="MYSQL数据库名称" placeholder="database"></mdui-text-field>
                <input type="submit">
            </form>
        </mdui-card>
        <div class="item"></div>
    </div>
<?php }?>
<script src="https://unpkg.com/mdui@2.0.3/mdui.global.js"></script>
</body>
</html>
