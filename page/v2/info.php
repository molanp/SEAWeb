<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/Config.class.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/until.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/path.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/__version__.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/logger.php");

req_log();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    include_once($_SERVER["DOCUMENT_ROOT"] . "/services/connect.php");
    $for = $_GET["for"] ?? NULL;
    switch ($for) {
        case "api":
            $urlPath = $_GET["url"] ?? "";
            preg_match("#/docs/(.*)#", $urlPath, $urlPath);
            $urlPath = addSlashIfNeeded($urlPath[1] ?? "");
            $statement = $DATABASE->prepare("SELECT name, version, author, method, profile, request, response, type, status FROM api WHERE url_path = :urlPath");
            $statement->execute([":urlPath" => $urlPath]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            $data = [];
            if ($result) {
                foreach ($result as $column => $value) {
                    $conname[$column] = $value;
                };
                $count = [];
                $stmt = $DATABASE->prepare("SELECT url, COUNT(*) AS count FROM access_log GROUP BY url");
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $count[$row["url"]] = $row["count"];
                };
                $conname["count"] = $count["/api" . $urlPath] ?? "0";
            } else {
                _return_("Not Found.", 404);
            }
            break;
        case "web":
            $conname = (new Data())->get("web");
            $conname["version"] = $__version__;
            break;
        case "status":
            $conname = [];
            $query = $DATABASE->prepare("SELECT name, status FROM api");
            $query->execute();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $conname[$row["name"]] = $row["status"];
            }
            break;
        default:
            include_once($_SERVER["DOCUMENT_ROOT"] . "/services/path.php");
            $pages = $_GET["page"] ?? 1;
            if (!is_numeric($pages) || $pages <= 0 || floor($pages) != $pages) {
                _return_("不合法的页码", 400);
            }
            $pages = ($pages - 1) * 12;
            $paths = getPath(PLUGIN_FOLDERS);
            $paths = array_map(function ($path) {
                return str_replace("/", DIRECTORY_SEPARATOR, $path);
            }, $paths);

            $pattern = "~(" . implode("|", $paths) . ")[/\\\\](.*)~";
            if (apineedupdate()) {
                $conname = [];
                $pluginFiles = str_replace(["/", "\\"], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], find_files(PLUGIN_FOLDERS, ".php"));
                if (count($pluginFiles) > 0) {
                    foreach ($pluginFiles as $pluginFilePath) {
                        include_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . $pluginFilePath;
                        $absolutePath = realpath($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . $pluginFilePath);
                        $file = basename($absolutePath);
                        $dir = dirname($absolutePath);
                        $pluginClassName = pathinfo($file, PATHINFO_FILENAME);
                        if (!class_exists($pluginClassName) && is_file($dir . DIRECTORY_SEPARATOR . $pluginClassName . ".php")) {
                            $pluginClassName = basename($dir);
                        }
                        if (class_exists($pluginClassName)) {
                            $plugin = new $pluginClassName();
                            if (method_exists($plugin, "getInfo") && method_exists($plugin, "run")) {
                                $info = $plugin->getInfo();
                                $type = $info["type"] ?? "一些工具";
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
                                $statusQuery = $DATABASE->prepare("SELECT status FROM api WHERE name = :name");
                                $statusQuery->execute([":name" => $info["name"]]);
                                $status = $statusQuery->fetchColumn();
                                $status = ($status === false) ? "true" : $status;
                                $maxIdQuery = $DATABASE->query("SELECT MAX(id) FROM api");
                                $maxId = $maxIdQuery->fetchColumn();
                                $id = $maxId + 1 ?? 0;
                                $data = [
                                    "id" => $id,
                                    "name" => $info["name"],
                                    "version" => $info["version"] ?? "1.0",
                                    "author" => $info["author"] ?? "Unknown",
                                    "method" => $info["method"] ?? "ALL",
                                    "profile" => $info["profile"],
                                    "request" => $info["request_par"],
                                    "response" => $info["return_par"],
                                    "class" => $pluginClassName,
                                    "url_path" => $path,
                                    "file_path" => $absolutePath,
                                    "type" => $type,
                                    "status" => (string) $status,
                                    "time" => time()
                                ];
                                UpdateOrCreate($DATABASE, "api", $data);
                            } else {
                                (new logger())->warn("插件类缺少 getInfo() 方法，文件路径：$pluginFilePath ，文件名：$file");
                            }
                        } else {
                            (new logger())->warn("插件文件缺少主类，文件路径：$pluginFilePath ，文件名：$file");
                        }
                    }
                }
                try {
                    $threshold = time() - (60 * 30);
                    $query_check = "SELECT COUNT(*) FROM api WHERE time < :threshold";
                    $stmt_check = $DATABASE->prepare($query_check);
                    $stmt_check->bindParam(":threshold", $threshold, PDO::PARAM_INT);
                    $stmt_check->execute();
                    $count = $stmt_check->fetchColumn();

                    if ($count > 0) {
                        $query_delete = "DELETE FROM api WHERE time < :threshold";
                        $stmt_delete = $DATABASE->prepare($query_delete);
                        $stmt_delete->bindParam(":threshold", $threshold, PDO::PARAM_INT);
                        $stmt_delete->execute();
                        $rowCount = $stmt_delete->rowCount();
                        (new logger())->info("已删除 $rowCount 条过期API记录。");
                    } else {
                        //(new logger())->info("没有过期API记录需要删除。");
                    }
                } catch (PDOException $e) {
                    (new logger())->error("删除过期API数据时出错: " . $e->getMessage());
                }
            };
            $count = [];
            $query = "SELECT url, COUNT(*) AS count FROM access_log GROUP BY url";
            $stmt = $DATABASE->prepare($query);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $count[$row["url"]] = $row["count"];
            }

            $query = $DATABASE->prepare("SELECT * FROM api ORDER BY name LIMIT 12 OFFSET $pages");
            $query->execute();
            $conname = [];
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $type = $row["type"];
                $name = $row["name"];
                $profile = $row["profile"];
                $url_path = $row["url_path"];
                $status = $row["status"];
                $conname[$type][$name] = [
                    "path" => $url_path,
                    "count" => $count["/api" . $url_path] ?? 0,
                    "api_profile" => $profile,
                    "status" => $status
                ];
            }
            break;
    }
    _return_($conname);
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once($_SERVER["DOCUMENT_ROOT"] . "/services/connect.php");
    $for = $_POST["for"] ?? NULL;
    $conname = [];
    switch ($for) {
        case "setting":
            if (tokentime($_POST)) {
                $query = $DATABASE->prepare("SELECT item, value, info FROM setting");
                $query->execute();
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $conname[$row["item"]] = [$row["value"], $row["info"]];
                }
            } else {
                code(401);
            }
            break;
        case "update":
            if (tokentime($_POST)) {
                include_once($_SERVER["DOCUMENT_ROOT"] . "/services/update.php");
                $dbData = [];
                $stmt = $DATABASE->query("SELECT item FROM setting");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $dbData[] = $row["item"];
                }
                $itemsToAdd = array_diff(array_keys(UP_SYS), $dbData);
                $itmsToDelete = array_diff($dbData, array_keys(UP_SYS));

                if (!empty($itemsToAdd)) {
                    $sqlAdd = "INSERT INTO setting (item, value, info) VALUES (:item, :value, :info)";
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
                (new logger())->info("用户执行更新设置项操作");
                $conname = "更新成功";
            } else {
                code(401);
            }
            break;
    }
    _return_($conname);
}
