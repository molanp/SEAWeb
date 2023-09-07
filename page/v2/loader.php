<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/path.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/services/connect.php');

$pluginFiles = preg_replace('/api\//',"",parse_url('http' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], PHP_URL_PATH));
if (check_files(PLUGIN_FOLDERS, $pluginFiles.'/index.php')) {
    $pluginFilePath = check_files(PLUGIN_FOLDERS,$pluginFiles.'/index.php');
} else {
    $pluginFilePath = check_files(PLUGIN_FOLDERS,$pluginFiles.'.php');
}
if ($pluginFilePath) {
    if (count($pluginFilePath)==1) {
        $pluginFilePath = $pluginFilePath[0];
        include_once $pluginFilePath;
        $pluginClassName = pathinfo($pluginFilePath, PATHINFO_FILENAME);
        if (!class_exists($pluginClassName)) {
            $pluginClassName = basename(dirname($pluginFilePath));
        }
        if (class_exists($pluginClassName)) {
            $plugin = new $pluginClassName();
            if (method_exists($plugin, 'run')) {
                if (handle_check($plugin->getInfo()['name'])) {
                    $plugin->run($_REQUEST);
                }
            } else {
                watchdog(404,"插件类缺少 run() 方法.File ".basename($pluginFilePath)." in {$pluginFilePath}");
            }
        } else {
            watchdog(404,"插件类未定义.File ".basename($pluginFilePath)." in {$pluginFilePath}");
        }
    } else {
        watchdog(404,"插件 path 不唯一.File in ".json_encode($pluginFilePath));
    }
} else {
    watchdog(404,"插件文件不存在.File ".basename($pluginFilePath)." in {$pluginFilePath}");
}
