<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/Config.class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/path.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/__version__.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/logger.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    include_once($_SERVER['DOCUMENT_ROOT'].'/services/connect.php');
    $WEB= new Config($_SERVER['DOCUMENT_ROOT'].'/data/web');
    $for = $_GET['for'] ?? NULL;
    switch($for) {
        case 'setting':
            if (isset($_GET['apikey']) && tokentime($_GET['apikey'])) {
                $conname = [];
                $query = DATABASE->prepare("SELECT item, value, info FROM setting");
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
            $query = DATABASE->prepare("SELECT name, status FROM api");
            $query->execute();
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
                                $statusQuery = DATABASE->prepare("SELECT status FROM api WHERE name = :name");
                                $statusQuery->execute([':name' => $info["name"]]);
                                $status = $statusQuery->fetchColumn();
                                $status = ($status === false) ? "true" : $status;
                                $maxIdQuery = DATABASE->query("SELECT MAX(id) FROM api");
                                $maxId = $maxIdQuery->fetchColumn();
                                $id = ($maxId !== false) ? ($maxId + 1) : 0;
                                $data = [
                                    "id" => $id,
                                    "name" => $info["name"],
                                    "version" => $info["version"]??"1.0",
                                    "author" => $info["author"]??"Unknown",
                                    "method" => $info["method"]??"GET",
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
                                UpdateOrCreate(DATABASE, "api", $data);
                            } else {
                                (new logger())->error("插件类缺少 getInfo() 方法，文件路径：$pluginFilePath ，文件名：$file");
                            }
                        }
                    }
                };
                try {
                    $threshold = time() - (60 * 30); // 计算30分钟前的时间戳
                    $query_check = "SELECT COUNT(*) FROM access_log WHERE time < :threshold";
                    $stmt_check = DATABASE->prepare($query_check);
                    $stmt_check->bindParam(':threshold', $threshold, PDO::PARAM_INT);
                    $stmt_check->execute();
                    $count = $stmt_check->fetchColumn();
                
                    if ($count > 0) {
                        $query_delete = "DELETE FROM access_log WHERE time < :threshold";
                        $stmt_delete = DATABASE->prepare($query_delete);
                        $stmt_delete->bindParam(':threshold', $threshold, PDO::PARAM_INT);
                        $stmt_delete->execute();
                        $rowCount = $stmt_delete->rowCount();
                        (new logger())->info("已删除 $rowCount 条过期API记录。");
                    } else {
                        //(new logger())->info("没有过期API记录需要删除。");
                    }
                } catch (PDOException $e) {
                    (new logger())->error("删除过期API数据时出错: " . $e->getMessage());
                }
                break;
            };
            //统计调用
            $count = [];
            $query = "SELECT url, COUNT(*) AS count FROM access_log GROUP BY url";
            $stmt = DATABASE->prepare($query);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $count[$row['url']] = $row['count'];
            }

            $query = DATABASE->prepare("SELECT * FROM api ORDER BY name");//id
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
                    'count' => $count["/api".$url_path] ?? 0,
                    'api_profile' => $profile,
                    'status' => $status
                ];
            }
            break;
    }
    _return_($conname);
}
?>