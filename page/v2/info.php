<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/Config.class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/path.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/__version__.php');

RequestLimit("10/min");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    load();
    $DATA = new Config($_SERVER['DOCUMENT_ROOT'].'/data/status');
    $WEB_= new Config($_SERVER['DOCUMENT_ROOT'].'/data/web');
    $for = $_GET['for'] ?? NULL;
    switch($for) {
        case 'web':
            if (cache('web')){
                $conname = cache('web');
            } else {
                $WEB = $WEB_->get('web');
                $keys = array_keys($WEB);
                foreach ($keys as $key) {
                    $conname["web"][$key] = $WEB[$key];
                }
                $conname["version"] = $__version__;
                $conname["setting"] = $WEB_->get('setting');
                cache('web',$conname);
            }
            break;
        case 'status':
            $conname = [];
            if (!cache('status')) {
                include_once($_SERVER['DOCUMENT_ROOT'].'/services/path.php');
                $paths = getPath(PLUGIN_FOLDERS);
                $paths = array_map(function($path) {
                    return str_replace('/', DIRECTORY_SEPARATOR, $path);
                }, $paths);
    
                $pattern = "~(".implode('|', $paths).")[/\\\\](.*)~";
                if (!cache('api')) {
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
                                    $conname[$info['name']] = $DATA->get($info['name'],true);
                                }
                            } else {
                                //error_log("插件类缺少 getInfo() 方法，文件路径：$pluginFilePath ，文件名：$file",3,LOGGER);
                            };
                            unset($plugin);
                        }
                    }
                    cache('status', $conname);
                }
            } else {
                $conname = cache('status');
            }
            break;
        default:
            include_once($_SERVER['DOCUMENT_ROOT'].'/services/path.php');
            $paths = getPath(PLUGIN_FOLDERS);
            $paths = array_map(function($path) {
                return str_replace('/', DIRECTORY_SEPARATOR, $path);
            }, $paths);

            $pattern = "~(".implode('|', $paths).")[/\\\\](.*)~";
            if (!cache('api')) {
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
                                $path = str_replace(
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
                                );
                    
                                $conname[$type][$info['name']] = [
                                    'path' => $path,
                                    'api_profile' => $info['profile'],
                                    'api_address' => re_add([$info['method']], ["/api" . $path => '-']),
                                    'version' => $info['version'],
                                    'author' => $info['author'],
                                    'request_parameters' => $info['request_par'],
                                    'return_parameters' => $info['return_par'],
                                    'status' => $DATA->get($info['name'], true)
                                ];
                                if ($DATA->get($info['name']) === '' || $DATA->get($info['name']) === null) {
                                    $DATA->set($info['name'], true)->save();
                                }
                            };
                            unset($plugin);
                        } else {
                            //error_log("插件类缺少 getInfo() 方法，文件路径：$pluginFilePath ，文件名：$file",3,LOGGER);
                        }
                    }
                    
                }
                cache('api', $conname);
            } else {
                $conname = cache('api');
            }
            break;
    }
    _return_($conname);
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    load();
    $WEB= new Config($_SERVER['DOCUMENT_ROOT'].'/data/web');
    $STATUS= new Config($_SERVER['DOCUMENT_ROOT'].'/data/status');

    $for = $_POST['for'] ?? NULL;
    switch($for) {
        case 'edit_web':
            if (isset($_POST['token']) && $_POST['token'] == $WEB->get('account')['password']) {
                $WEB->set("web",[
                    "record"=>$_POST["record"],
                    "index_title"=>$_POST["index_title"],
                    "copyright"=>$_POST["copyright"],
                    "index_description"=>$_POST["index_description"],
                    "notice"=>[
                        "data"=>$_POST["notice"],
                        "latesttime"=>date('Y-m-d')],
                    "keywords"=>$_POST["keywords"],
                    "links"=>$_POST["links"]])->save();

                $SETTING = $WEB->get("setting");

                foreach ($_POST["setting"] as $key => $value) {
                    foreach($value as $key => $value) {
                        if (array_key_exists($key, $SETTING)) {
                            $SETTING[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                        }
                    }
                }
                $WEB->set("setting", $SETTING)->save();
                del_cache('web');
                _return_("修改成功");
            } else {
                _return_("身份验证失败",403);
            }
            break;
        case 'edit_status':
            if (isset($_POST['token']) && $_POST['token'] == $WEB->get('account')['password']) {
                $keys = array_keys($_POST["data"]);
                $data = array_values($_POST["data"]);
                $i = 0;
                for ($i;$i<count($data);$i++) {
                    $STATUS->set($keys[$i],filter_var($data[$i], FILTER_VALIDATE_BOOLEAN))->save();
                }
                del_cache('status');
                _return_("修改成功");
            } else {
                _return_("身份验证失败",403);
            }
            break;
        }
}
?>