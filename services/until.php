<?php
include_once('Config.class.php');

//组合table
function re_add($type=[],$url=[],$info=[]) {
    $table = "|Method|URL|Info|\n|---|---|---|";
    for($i=0; $i<count($type); $i++) {
        $table .= "\n|{$type[$i]}|[{$url[$i]}]({$url[$i]})|{$info[$i]}|";
    };
    return $table;
}
function re_par($key=[],$info=[]) {
    $table = "|Key|Info|\n|---|---|";
    for($i=0; $i<count($key); $i++) {
        $table .= "\n|`{$key[$i]}`|{$info[$i]}|";
    };
    return $table;
}
//curl_get获取数据
function curl_get($url, $data = [], $code = false)
{
    if ($url == "") {
        return false;
    }
    $url = $url . '?' . http_build_query($data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.80 Safari/537.36");
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $output = curl_exec($ch);
    curl_close($ch);
    if (curl_exec($ch) === false) {
        return curl_error($ch);
    }
    if ($code === true) {
        return curl_getinfo($ch, CURLINFO_HTTP_CODE);
    }
    $json = json_decode($output, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        return $json;
    } else {
        return (string) $output;
    }
}
//return
function _return_($context,$status=200,$location=false) {
    header('Access-Control-Allow-Origin: *'); // 允许跨域请求
    header('Access-Control-Allow-Methods: POST,GET,OPTIONS,DELETE,PUT'); // 允许全部请求类型
    header('Access-Control-Allow-Credentials: true'); // 允许发送 cookies
    header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 允许自定义请求头的字段
    //header("HTTP/1.1 $status");
    if ($location == false) {
        header('Content-type:text/json;charset=utf-8');
        die(json_encode(['status'=>$status,'data'=>$context,'time'=>time()],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
    } else {
        die(header("Location: $context"));
    }
}
//Error http
function Error($errno, $errstr, $errfile, $errline) {
    header("HTTP/1.1 500");
    //error_log(date(Y-m-d H:i:s)."[$errno] $errstr in $errfile line $errline.", 1,"logs/log.log");
    _return_("Error:[$errno] $errstr in $errfile line $errline.",500);//";)
}
set_error_handler("Error");
//check status
function handle_check() {
    if (strpos($_SERVER['REQUEST_URI'], 'api/') !== false) {
        global $api_name;
        $DATA = new Config($_SERVER['DOCUMENT_ROOT'].'/data/status');
        $status=$DATA->get($api_name,true);
        if ($status !== true) {
            header("HTTP/1.1 406");
            _return_("API已关闭",406);
        } else {
            return true;
        }
    } else {
        return false;
    }
}

//递归查询
function find_files($dir, $prefix = '') {
    $files = glob("$dir/*/index.php"); // 查找所有名称为index.php的文件
    $relative_paths = array(); // 存储相对路径的数组

    foreach ($files as $file) {
        $relative_path = $prefix . trim(str_replace($dir, '', $file), "/"); // 获取相对路径
        $relative_paths[] = $relative_path;
    }

    // 递归查找子文件夹
    $subdirs = glob("$dir/*", GLOB_ONLYDIR);
    foreach ($subdirs as $subdir) {
        $relative_paths = array_merge($relative_paths, find_files($subdir, $prefix . basename($subdir) . '/'));
    }

    return $relative_paths;
}
//初始化
function load() {
    $DATA = new Config($_SERVER['DOCUMENT_ROOT'].'/data/web');
    if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/data')) {
        mkdir($_SERVER['DOCUMENT_ROOT'].'/data');
        $DATA->set("account",["username"=>"admin","password"=>hash('sha256', 'password')])->save();
        $DATA->set("web",[
            "record"=>"",
            "index_title"=>"SEAWeb",
            "copyright"=>"",
            "index_description"=>"这是网站简介，这里支持*MarkDown*语法",
            "notice"=>[
                "data"=>"> **这里也支持markdown语法**\n\n欢迎使用SEAWeb，本模板由[molanp](https://github.com/molanp)开发与维护。目前正在不断完善~\n如果你觉得这个API有什么不完善的地方或者说你有什么更好的想♂法，可以在[issues](https://github.com/molanp/easyapi_wesbite/issues)上提出建议",
                "latesttime"=>date('Y-m-d')],
            "keywords"=>"API,api",
            "links"=>"[GitHub](https://github.com/molanp/SEAWeb)\n[Issues](https://github.com/molanp/SEAWeb/issues)\n[开发指南](https://molanp.github.io/SEAWeb_docs)"])->save();
    }
}
/**
 * 缓存函数
 *
 * @param string $name 缓存文件名，包含目录路径和文件名，例如：cc/test
 * @param mixed $data 缓存数据
 * @return mixed 返回缓存数据（如果存在且未过期），否则返回 null
 */
function cache($name, $data=null) {
    $path = $_SERVER['DOCUMENT_ROOT'].'/cache';
    // 定义缓存文件名和有效期
    $filename = $_SERVER['DOCUMENT_ROOT'].'/cache/'.$name;
    $expiration = 60 * 20; // 20分钟
    
    // 如果缓存文件存在且未过期，则读取缓存数据并返回
    if (file_exists($filename) && time() - filemtime($filename) < $expiration) {
        return json(file_get_contents($filename));
    }

    if ($data===null && !file_exists($filename)) {
        return null;
    }
    
    // 否则，如果目录不存在，则创建目录
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
    
    // 创建新的缓存文件并写入数据
    file_put_contents($filename, json($data));
    
    // 删除过期的缓存文件
    $files = array_diff(scandir($path), array('.', '..'));
    foreach ($files as $file) {
        $filePath = "$path/$file";
        if (is_dir($filePath) && count(glob($filePath . '/*')) === 0) {
            rmdir($filePath);
        } elseif (is_file($filePath) && time() - filemtime($filePath) >= $expiration) {
            unlink($filePath);
        }
    }
}
/**
 * 删除指定的缓存文件或文件夹
 *
 * @param string $name 要删除的缓存文件或文件夹的名称
 * @return bool 删除成功返回 true，删除失败返回 false
 */
function del_cache($name) {
    $cachePath = $_SERVER['DOCUMENT_ROOT'].'/cache/' . $name;
    if (is_dir($cachePath)) {
        $files = array_diff(scandir($cachePath), array('.', '..'));
        foreach ($files as $file) {
            del_cache($name . '/' . $file);
        }
        return rmdir($cachePath);
    } elseif (file_exists($cachePath)) {
        return unlink($cachePath);
    } else {
        return false;
    }
}
/**
 * 尝试将 JSON 字符串解析为数组，或将数组转换为 JSON 字符串
 *
 * @param string|array $json 要解析的 JSON 字符串或要转换的数组
 * @return array|string 返回解析后的数组或数组转换后的 JSON 字符串，如果解码和编码都失败则返回原始字符串
 */
function json($json) {
    if (is_array($json)) {
        $encoded = json_encode($json,JSON_PRETTY_PRINT);
        if ($encoded !== false) {
            return $encoded;
        } else {
            return $json;
        }
    } else {
        $decoded = json_decode($json, true);
        if ($decoded !== null) {
            return $decoded;
        } else {
            return $json;
        }
    }
}
?>