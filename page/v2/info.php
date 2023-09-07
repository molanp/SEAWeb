<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/Config.class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/path.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/__version__.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    include_once($_SERVER['DOCUMENT_ROOT'].'/services/connect.php');
    $WEB= new Config($_SERVER['DOCUMENT_ROOT'].'/data/web');
    $for = $_GET['for'] ?? NULL;
    switch($for) {
        case 'setting':
            if (isset($_GET['apikey']) && tokentime($_GET['apikey'])) {
                $conname = [];
                $query = $database->prepare("SELECT item, value, info FROM setting");
                $query->execute();
                // 遍历查询结果并将其添加到关联数组中
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $conname[$row['item']] = [$row['value'], $row["info"]];
                }
                break;
            } else {
                _return_("权限不足", 403);
            }
        case 'web':
            $WEB = $WEB->get('web');
            $keys = array_keys($WEB);
            foreach ($keys as $key) {
                $conname[$key] = $WEB[$key];
            }
            $conname["version"] = $__version__;
            break;
        case 'status':
            $conname = [];
            $query = $database->prepare("SELECT name, status FROM api");
            $query->execute();
            // 遍历查询结果并将其添加到关联数组中
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $conname[$row['name']] = $row['status'];
            }
            break;
        default:
            include_once($_SERVER['DOCUMENT_ROOT'].'/services/path.php');
            $paths = getPath(PLUGIN_FOLDERS);
            $paths = array_map(function($path) {
                return str_replace('/', DIRECTORY_SEPARATOR, $path);
            }, $paths);

            $pattern = "~(".implode('|', $paths).")[/\\\\](.*)~";
            if (apineedupdate()) {
                $time = time();
                $conname = [];
                $pluginFiles = str_replace(['/','\\'], [DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR], find_files(PLUGIN_FOLDERS,'.php'));
                if (count($pluginFiles) > 0) {
                    foreach ($pluginFiles as $pluginFilePath) {
                        include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$pluginFilePath;
                        $absolutePath = realpath($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$pluginFilePath);
                        $file = basename($absolutePath);
                        $dir = dirname($absolutePath);
                        $pluginClassName = pathinfo($file, PATHINFO_FILENAME);
                        if (!class_exists($pluginClassName) && is_file($dir . DIRECTORY_SEPARATOR . $pluginClassName . '.php')) {
                            $pluginClassName = basename($dir);
                        }
                        if (class_exists($pluginClassName)) {
                            $plugin = new $pluginClassName();
                            if (method_exists($plugin, 'getInfo')&&method_exists($plugin, 'run')) {
                                $info = $plugin->getInfo();
                                $type = $info['type'] ?? '一些工具';
                                $path = addSlashIfNeeded(str_replace(
                                    PLUGIN_FOLDERS,
                                    "",
                                    str_replace(
                                        "\\",
                                        "/",
                                        str_replace(
                                            ".php",
                                            "",
                                             str_replace(
                                                "index.php",
                                                "",
                                                $absolutePath
                                            )
                                        )
                                    )
                                ));
                                // 假设 $data 是要插入的关联数组，其中键是列名，值是对应的数据
                                $status = $database->query("SELECT status FROM api WHERE name = '" . $info["name"] . "'")->fetchColumn();
$status = ($status === false) ? "true" : $status;
                                $data = [
                                    "id" => $database->query("SELECT MAX(id) FROM api")->fetchColumn() + 1?? 0,
                                    "name" => $info["name"],
                                    "version" => $info["version"],
                                    "author" => $info["author"],
                                    "method" => $info["method"],
                                    "profile" => $info["profile"],
                                    "request" => $info["request_par"],
                                    "response" => $info["return_par"],
                                    "class" => $pluginClassName,
                                    "url_path" => $path,
                                    "file_path" => $absolutePath,
                                    "type" => $type,
                                    "status" => (string) $status,
                                    "time" => $time
                                ];
                                
                                // 构建检查是否存在记录的 SQL 语句
                                $checkSql = "SELECT COUNT(*) FROM api WHERE name = :name AND type = :type AND file_path = :file_path";
                                
                                // 使用预处理语句执行检查
                                $checkStatement = $database->prepare($checkSql);
                                $checkStatement->execute([
                                    ":name" => $info["name"],
                                    ":type" => $type,
                                    ":file_path" => $absolutePath
                                ]);
                                
                                // 检查是否存在相同名字和类型但不同 file_path 的记录
                                if ($checkStatement->fetchColumn() == 0) {
                                    // 不存在相同记录，执行插入操作
                                    UpdateOrCreate($database, "api", $data, ["name"=>$info["name"]]);
                                } else {
                                    // 存在相同记录，执行其他操作（例如跳过插入或执行其他逻辑）
                                    // 在这里添加你的处理逻辑
                                    // 例如：echo "已存在相同记录，跳过插入";
                                }
                        } else {
                            //error_log("插件类缺少 getInfo() 方法，文件路径：$pluginFilePath ，文件名：$file",3,LOGGER);
                        }
                    }
                }
                $query = $database->prepare("SELECT * FROM api ORDER BY name");//id
                $query->execute();
                $conname = [];
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $type = $row['type'];
                    $name = $row['name'];
                    $profile = $row['profile'];
                    $url_path = $row['url_path'];
                    $status = $row['status'];
                
                    // 构建数组项
                    $conname[$type][$name] = [
                        'path' => $url_path,
                        'api_profile' => $profile,
                        'status' => $status
                    ];
                }
            }
            break;
            //delete
            } else {
                $query = $database->prepare("SELECT * FROM api ORDER BY name");//id
                $query->execute();
                $conname = [];
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $type = $row['type'];
                    $name = $row['name'];
                    $profile = $row['profile'];
                    $url_path = $row['url_path'];
                    $status = $row['status'];
                
                    // 构建数组项
                    $conname[$type][$name] = [
                        'path' => $url_path,
                        'api_profile' => $profile,
                        'status' => $status
                    ];
                }
            }
            break;
    }
    _return_($conname);
}
?>