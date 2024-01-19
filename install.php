<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" />
    <meta name="renderer" content="webkit" />
    <meta name="force-rendering" content="webkit" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <link rel="Shortcut Icon" href="/favicon.ico">
    <link rel="bookmark" href="/favicon.ico" type="image/x-icon" />
    <title>安装SEAWeb</title>
    <link rel="stylesheet" href="https://registry.npmmirror.com/mdui/2.0.3/files/mdui.css" />
    <link rel="stylesheet" href="/assets/css/style.css" />
</head>

<body class="mdui-theme-auto">
    <?php
    if (file_exists("configs/config.php")) {
        include_once($_SERVER["DOCUMENT_ROOT"] . "/services/connect.php");
        $DATABASE->exec("DROP INDEX idx_api ON api");
        $DATABASE->exec("CREATE INDEX idx_api ON api (name(50))");?>
        <div class="grid">
            <div></div>
            <mdui-card>
                <h3>已取消本次安装操作</h3>
                <mdui-button href="/">主页</mdui-button>
                <mdui-button href="/sw-ad">管理员登录</mdui-button>
            </mdui-card>
            <div></div>
        </div>
        <?php
        @unlink("install.php");
    } else {
        include_once($_SERVER["DOCUMENT_ROOT"] . "/services/update.php");
        if (isset($_POST["action"]) && $_POST["action"] == "install") {
            try {
                mkdir("configs");
                $db = $_POST["mysql_database"];
                $bind = "mysql:host={$_POST["mysql_host"]};dbname={$db}";
                $username = $_POST["mysql_username"];
                $password = $_POST["mysql_password"];
                $DATABASE = new PDO($bind, $username, $password);
                $config = "<?php\n";
                $config .= "\t\$bind = \"{$bind}\";\n";
                $config .= "\t\$mysql_username = \"{$username}\";\n";
                $config .= "\t\$mysql_password = \"{$password}\";";

                $file = fopen("configs/config.php", "w");
                fwrite($file, $config);
                fclose($file);
                include_once($_SERVER["DOCUMENT_ROOT"] . "/services/Config.class.php");
                $data = new Data();
                $DATABASE->exec("CREATE TABLE IF NOT EXISTS data (
                item TEXT,
                content TEXT,
                belong TEXT,
                time TEXT
            )");
                try {
                    $DATABASE->exec("CREATE UNIQUE INDEX idx_data ON data (item(50), belong(50))");
                } catch (Exception $e) {
                }
                $data->set('web', [
                    "record" => "",
                    "index_title" => "SEAWeb",
                    "copyright" => "molanp 2023",
                    "index_description" => "这是网站简介，这里支持*MarkDown*语法",
                    "notice" => "> **这里也支持markdown语法**\n\n欢迎使用SEAWeb，本模板由[molanp](https://github.com/molanp)开发与维护。目前正在不断完善~\n如果你觉得这个API有什么不完善的地方或者说你有什么更好的想法，可以在[issues](https://github.com/molanp/SEAWeb/issues)上提出建议",
                    "keywords" => "API,api",
                    "links" => "[GitHub](https://github.com/molanp/SEAWeb)\n[Issues](https://github.com/molanp/SEAWeb/issues)\n[开发指南](https://molanp.github.io/SEAWeb_docs)"
                ]);
                $DATABASE->exec("CREATE TABLE IF NOT EXISTS users (
                username TEXT,
                password TEXT,
                token TEXT,
                apikey TEXT,
                permission INTEGER,
                regtime TEXT,
                logtime TEXT
            )");
                try {
                    $DATABASE->exec("CREATE INDEX idx_username ON users (username(50))");
                } catch (Exception $e) {
                }


                $stmt = $DATABASE->prepare("REPLACE INTO users (username, password, regtime, permission) VALUES (:username, :password, :regtime, :permission)");
                $stmt->bindParam(":username", $_POST["usr"]);
                $stmt->bindValue(":password", hash("sha256", $_POST["pwd"]));
                $stmt->bindValue(":regtime", date("Y-m-d H:i:s"));
                $stmt->bindValue(":permission", 9);
                $stmt->execute();

                $DATABASE->exec("CREATE TABLE IF NOT EXISTS setting (
                item TEXT,
                value TEXT,
                info TEXT
            )");
                try {
                    $DATABASE->exec("CREATE INDEX idx_setting ON setting (item(50))");
                } catch (Exception $e) {
                }

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
                method TEXT,
                referer TEXT,
                param TEXT,
                agent TEXT
            )");
                try {
                    $DATABASE->exec("CREATE INDEX idx_access ON access_log (time(50), ip(50), url(50), referer(50))");
                } catch (Exception $e) {
                }

                $DATABASE->exec("CREATE TABLE IF NOT EXISTS log (
                time TEXT,
                level TEXT,
                content TEXT
            )");
                try {
                    $DATABASE->exec("CREATE INDEX idx_log ON log (time(50), level(50), content(50))");
                } catch (Exception $e) {
                }

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
                try {
                    $DATABASE->exec("CREATE INDEX idx_api ON api (name(50))");
                } catch (Exception $e) {
                }
        ?>
                <div class="grid">
                    <div></div>
                    <mdui-card>
                        <h3>安装成功</h3>
                        <mdui-button href="/">主页</mdui-button>
                        <mdui-button href="/sw-ad">管理员登录</mdui-button>
                    </mdui-card>
                    <div></div>
                </div>
            <?php
                @unlink("install.php");
            } catch (Exception $e) {
                @unlink("configs/config.php");
                @rmdir("configs");
            ?>
                <div class="grid">
                    <div></div>
                    <mdui-card>
                        <h3>安装失败</h3>
                        <mdui-button href="/">重试</mdui-button>
                        <p>原因如下</p>
                        <mdui-text-field autosize readonly value="<?= $e ?>"></mdui-text-field>
                    </mdui-card>
                    <div></div>
                </div>
            <?php }
        } else {
            include_once($_SERVER["DOCUMENT_ROOT"] . "/services/__version__.php"); ?>
            <div class="grid">
                <div></div>
                <mdui-card>
                    <h3>欢迎使用SEAWeb</h3>
                    <p>你正在安装<b>SEAWeb <?= $__version__ ?></b></p>
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
                <div></div>
            </div>
    <?php }
    } ?>
    <script src="https://registry.npmmirror.com/mdui/2.0.3/files/mdui.global.js"></script>
</body>

</html>