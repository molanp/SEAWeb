<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/Config.class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/path.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/__version__.php');
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    load();
    $DATA = new Config($_SERVER['DOCUMENT_ROOT'].'/data/status');
    $WEB= new Config($_SERVER['DOCUMENT_ROOT'].'/data/web');
    $for = $_GET['for'] ?? NULL;
    switch($for) {
        case 'web':
            if (cache('web')){
                $conname = cache('web');
            } else {
                $WEB = $WEB->get('web');
                $conname = [
                    "record"=>$WEB['record'],
                    "index_title"=>$WEB['index_title'],
                    "copyright"=>$WEB['copyright'],
                    "index_description"=>$WEB['index_description'],
                    "notice"=>$WEB['notice'],
                    "keywords"=>$WEB['keywords'],
                    "links"=>$WEB['links'],
                    "version"=>$__version__
                ];
                cache('web',$conname);
            }
            break;
        case 'status':
            $conname = [];
            // 检查缓存是否存在，若不存在则重新搜索目录
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
                            // 加载插件
                            include_once $pluginFilePath;
                            
                            // 获取插件文件的绝对路径
                            $absolutePath = realpath($pluginFilePath);
                    
                            // 获取文件名和目录名
                            $file = basename($absolutePath);
                            $dir = dirname($absolutePath);
                    
                            // 构建类名
                            $pluginClassName = pathinfo($file, PATHINFO_FILENAME);
                    
                            // 检查类是否存在
                            if (!class_exists($pluginClassName) && is_file($dir . DIRECTORY_SEPARATOR . $pluginClassName . '.php')) {
                                $pluginClassName = basename($dir);
                            }
                            // 检查类是否存在
                            if (class_exists($pluginClassName)) {
                                // 实例化插件类
                                $plugin = new $pluginClassName();
                                // 检查插件类是否有 getInfo() 方法
                                if (method_exists($plugin, 'getInfo')&&method_exists($plugin, 'run')) {
                                    // 调用插件方法并获取插件信息
                                    $info = $plugin->getInfo();
                                    $conname[$info['name']] = $DATA->get($info['name'],true);
                                }
                            } else {
                                //error_log("插件类缺少 getInfo() 方法，文件路径：$pluginFilePath ，文件名：$file",3,LOGGER);
                            }
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
                        // 加载插件
                        include_once $pluginFilePath;
                        
                        // 获取插件文件的绝对路径
                        $absolutePath = realpath($pluginFilePath);
                
                        // 获取文件名和目录名
                        $file = basename($absolutePath);
                        $dir = dirname($absolutePath);
                
                        // 构建类名
                        $pluginClassName = pathinfo($file, PATHINFO_FILENAME);
                
                        // 检查类是否存在
                        if (!class_exists($pluginClassName) && is_file($dir . DIRECTORY_SEPARATOR . $pluginClassName . '.php')) {
                            $pluginClassName = basename($dir);
                        }
                        // 检查类是否存在
                        if (class_exists($pluginClassName)) {
                            // 实例化插件类
                            $plugin = new $pluginClassName();
                            // 检查插件类是否有 getInfo() 方法
                            if (method_exists($plugin, 'getInfo')&&method_exists($plugin, 'run')) {
                                // 调用插件方法并获取插件信息
                                $info = $plugin->getInfo();
                                $type = $info['type'] ?? '一些工具';
                                preg_match($pattern, $pluginFilePath, $path);
                                $path = str_replace("\\","/",str_replace(".php","",str_replace("index.php","",$path[2])));

                                $conname[$type][$info['name']] = [
                                    'path'=>$path,
                                    'api_profile'=>$info['profile'],
                                    'api_address'=>re_add([$info['method']],["/api/".$path=>'-']),
                                    'version'=>$info['version'],
                                    'author'=>$info['author'],
                                    'request_parameters'=>$info['request_par'],
                                    'return_parameters'=>$info['return_par'],
                                    'status'=>$DATA->get($info['name'],true)
                                ];
                                if ($DATA->get($info['name']) === '' || $DATA->get($info['name']) === null) {
                                    $DATA->set($info['name'],true)->save();
                                }
                            }
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
                    del_cache('web');
                _return_("修改成功");
            } else {
                _return_("身份验证失败",403);
            }
            break;
        case 'edit_status':
            if (isset($_POST['token']) && $_POST['token'] == $WEB->get('account')['password']) {
                $STATUS= new Config($_SERVER['DOCUMENT_ROOT'].'/data/status');
                $keys = array_keys($_POST["data"]);
                $data = array_values($_POST["data"]);
                $i = 0;
                for ($i;$i<count($data);$i++) {
                    $STATUS->set($keys[$i],filter_var($data[$i], FILTER_VALIDATE_BOOLEAN))->save();
                }
                del_cache('api');
                _return_("修改成功");
            } else {
                _return_("身份验证失败",403);
            }
            break;
        }
}
?>