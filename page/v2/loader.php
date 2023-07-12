<?php
// 定义插件文件夹数组
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');
include_once $_SERVER['DOCUMENT_ROOT'].'/services/path.php';

$pluginFiles = preg_replace('/api\//',"",parse_url('http' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], PHP_URL_PATH));
// 构建插件文件路径
if (check_files(PLUGIN_FOLDERS, $pluginFiles.'/index.php')) {
    $pluginFilePath = check_files(PLUGIN_FOLDERS,$pluginFiles.'/index.php');
} else {
    $pluginFilePath = check_files(PLUGIN_FOLDERS,$pluginFiles.'.php');
}

// 加载插件文件
if ($pluginFilePath) {
    if (count($pluginFilePath)==1) {
        $pluginFilePath = $pluginFilePath[0];
        include_once $pluginFilePath;

        // 获取插件类名（这里假设插件类名与文件名相同，首字母大写）
        $pluginClassName = pathinfo($pluginFilePath, PATHINFO_FILENAME);

        if (!class_exists($pluginClassName)) {
            $pluginClassName = basename(dirname($pluginFilePath));
        }

        // 检查类是否存在
        if (class_exists($pluginClassName)) {
            // 实例化插件类
            $plugin = new $pluginClassName();

            // 检查插件类是否有 run() 方法
            if (method_exists($plugin, 'run')) {
                //检查是否开启
                if (handle_check($plugin->getInfo()['name'])) {
                    // 调用插件方法
                    $plugin->run($_REQUEST);
                }
            } else {
                // 插件类缺少 run() 方法
                watchdog(404,"插件类缺少 run() 方法.File ".basename($pluginFilePath)." in {$pluginFilePath}");
            }
        } else {
            // 插件类未定义
            watchdog(404,"插件类未定义.File ".basename($pluginFilePath)." in {$pluginFilePath}");
        }
    } else {
        watchdog(404,"插件 path 不唯一.File in ".json_encode($pluginFilePath));
    }

} else {
    // 插件文件不存在
    watchdog(404,"插件文件不存在.File ".basename($pluginFilePath)." in {$pluginFilePath}");
}
