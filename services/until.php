<?php
include_once('Config.class.php');

/**
 * 检查给定文件夹中是否存在指定的文件
 *
 * @param array $folders 文件夹路径的数组
 * @param string $file 要检查的文件名
 * @return array|bool 如果文件存在，则返回包含文件路径的数组；如果文件不存在，则返回false
 */
function check_files($floders,$file) {
    $files = [];
    foreach ($floders as $floder) {
        if(file_exists($floder.$file)) {
            $files[] = $floder.$file;
        }
    }
    if(count($files) > 0) {
        return $files;
    }
    return False;
}
/**
 * 重新格式化参数为 Markdown 表格
 *
 * @param array $type 请求方法数组
 * @param array $url 请求 URL 及信息数组
 * @return string 返回格式化后的 Markdown 表格
 */
function re_add($type=[],$url=[]) {
    $table = "|Method|URL|Info|\n|---|---|---|";
    $info = array_values($url);
    $url = array_keys($url);
    for($i=0; $i<count($type); $i++) {
        $table .= "\n|{$type[$i]}|[{$url[$i]}]({$url[$i]})|{$info[$i]}|";
    };
    return $table;
}
/**
 * 重新格式化参数为 Markdown 表格
 *
 * @param array $key 参数键和参数值数组
 * @return string 返回格式化后的 Markdown 表格
 */
function re_par($key=[]) {
    $table = "|Key|Info|\n|---|---|";
    $info = array_values($key);
    $key = array_keys($key);
    for($i=0; $i<count($key); $i++) {
        $table .= "\n|`{$key[$i]}`|{$info[$i]}|";
    };
    return $table;
}
/**
 * 使用 CURL 发起 GET 请求获取数据
 *
 * @param string $url 请求的 URL
 * @param array $data 请求参数，可选，默认为空数组
 * @param bool $code 是否返回 HTTP 状态码，可选，默认为 false
 * @return mixed 返回请求结果，如果发生错误，则返回错误信息
 */
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
/**
 * 返回结果
 *
 * @param mixed $context 返回的数据内容
 * @param int $status 状态码，默认为 200
 * @param bool|string $location 是否重定向，如果为字符串，则表示重定向的 URL
 * @return void
 */
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
        die(header("Location: $location"));
    }
}
/**
 * 自定义错误处理函数
 *
 * @param int $errno 错误级别
 * @param string $errstr 错误信息
 * @param string $errfile 发生错误的文件名
 * @param int $errline 发生错误的行号
 * @return void
 */
function watchdog($errno,$errstr=NULL, $errfile=NULL, $errline=NULL) {
    header("HTTP/1.1 500");
    //error_log(date(Y-m-d H:i:s)."[$errno] $errstr in $errfile line $errline.", 1,"logs/log.log");
    if (isset($errfile,$errline)) {
        $message = "Error[$errno]: $errstr in $errfile line $errline.";
    } elseif(isset($errstr)) {
        $message = "Error[$errno]: $errstr";
    } else {
        $message = "Error: $errno";
    }
    _return_($message,500);
}
set_error_handler("watchdog");
set_exception_handler("watchdog");
/**
 * 检查 API 状态
 *
 * @return bool 返回是否需要处理 API 请求
 */
function handle_check($api_name) {
    $DATA = new Config($_SERVER['DOCUMENT_ROOT'].'/data/status');
    $WEB= new Config($_SERVER['DOCUMENT_ROOT'].'/data/web');
    $status=$DATA->get($api_name,true);
    if ($status !== true || $WEB->get('__system__',false)===true) {
        header("HTTP/1.1 406");
        _return_("API already closed",406);
    } else {
        return true;
    }
}
/**
 * 递归查询文件
 *
 * @param array $dirs 目录路径数组
 * @param string $file 需要查询的文件名，可选，默认为.php文件，支持末尾字查询
 * @param string $prefix 前缀，可选，默认为空字符串
 * @return array 返回文件的相对路径数组
 */
function find_files($dirs, $file = '.php', $prefix = '') {
    $relative_paths = [];
    
    foreach ($dirs as $dir) {
        if (is_dir($dir)) {
            // 处理文件夹结果
            $subdirs = glob("$dir/*", GLOB_ONLYDIR);
            // 处理文件结果
            $files = glob("$dir/*$file");

            foreach ($files as $filePath) {
                if (is_file($filePath)) {
                    $relativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $filePath);
                    $relativePath = ltrim($relativePath, '\\/');
                    $relative_paths[] = $prefix . $relativePath;
                }
            }

            $subfiles = find_files($subdirs, $file, $prefix);
            $relative_paths = array_merge($relative_paths, $subfiles);
        }
    }
    
    return $relative_paths;
}


/**
 * 初始化函数
 *
 * @return void
 */
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
        $DATA->set("__system__",false)->save();
    }
}
/**
 * 缓存函数
 *
 * @param string $name 缓存文件名，包含目录路径和文件名，例如：cc/test
 * @param mixed $data 缓存数据
 * @return mixed 返回缓存数据（如果存在且未过期），否则返回 NULL
 */
function cache($name, $data=null) {
    $path = $_SERVER['DOCUMENT_ROOT'].'/cache';
    $filename = $_SERVER['DOCUMENT_ROOT'].'/cache/'.$name;
    $expiration = 60 * 20; // 20分钟
    if (file_exists($filename) && (time() - filemtime($filename)) < $expiration) {
        return json_decode(file_get_contents($filename));
    }
    if ($data===null && !file_exists($filename)) {
        return null;
    }
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
    file_put_contents($filename, json_encode($data,JSON_PRETTY_PRINT));
    $files = array_diff(scandir($path), array('.', '..'));
    foreach ($files as $file) {
        $filePath = "$path/$file";
        if (is_dir($filePath) && count(glob($filePath . '/*')) === 0) {
            rmdir($filePath);
        } elseif (is_file($filePath) && (time() - filemtime($filePath)) >= $expiration) {
            unlink($filePath);
        }
    }
    return null;
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
/**
 * 检查请求次数是否超过限制
 * 此函数应在 handle_check() 后执行
 *
 * @param string $limit 最大请求次数和时间单位，例如 '3/s' '10/min', '5/hour', '100/day'
 * @param string $name 如果引用文件中没有$api_name变量，请在此处自定义标识符
 * @return void
 */
function RequestLimit($limit,$name=null) {
    global $api_name;
    $api_name = $api_name ?? $name;
    $DATA = new Config($_SERVER['DOCUMENT_ROOT'].'/data/limit');
    $requests = $DATA->get('requests',[]);
    $lastRequestTime = $DATA->get('lastRequestTime',[]);
    $ip = $_SERVER['REMOTE_ADDR'];
    $currentTime = time();
    preg_match('/(\d+)\s*\/\s*(\w+)/', $limit, $matches);
    $quantity = intval($matches[1]);
    $unit = strtolower($matches[2]);
    switch ($unit) {
        case 's':
            $interval = $quantity;
            break;
        case 'min':
            $interval = $quantity * 60;
            break;
        case 'hour':
            $interval = $quantity * 60 * 60;
            break;
        case 'day':
            $interval = $quantity * 60 * 60 * 24;
            break;
        default:
            throw new Exception("Time units are only supported as s, min, hour, day");
    }
    if(isset($requests[$api_name][$ip]) && isset($lastRequestTime[$api_name][$ip])) {
        if($currentTime - $lastRequestTime[$api_name][$ip] > $interval) {
            $requests[$api_name][$ip] = 1;
            $lastRequestTime[$api_name][$ip] = $currentTime;
        } else {
            $requests[$api_name][$ip]++;
        }
    } else {
        $requests[$api_name][$ip] = 1;
        $lastRequestTime[$api_name][$ip] = $currentTime;
    }
    $DATA->set('requests',$requests)->save();
    $DATA->set('lastRequestTime',$lastRequestTime)->save();
    if($requests[$api_name][$ip] > $quantity) {
        _return_('请求次数超过限制(Too Many Requests)',429);
    }
}
/**
 * 获取路径数组相对于根路径的路径
 * @param array $paths 路径数组
 * @param string $root 根路径，默认为根目录
 * @return array 相对路径数组
 */
function getPath($paths, $root=NULL) {
    $DIRECTORY_SEPARATOR = DIRECTORY_SEPARATOR;
    if (empty($root)) {
        $root = $_SERVER['DOCUMENT_ROOT'];
    }

    // 如果根路径不以斜杠结尾，则添加斜杠
    if (substr($root, -1) != $DIRECTORY_SEPARATOR) {
        $root .= $DIRECTORY_SEPARATOR;
    }
    
    // 将根路径中的斜杠转换为系统默认类型
    $root = str_replace('/', $DIRECTORY_SEPARATOR, $root);

    // 将路径数组中的斜杠转换为系统默认类型并转义反斜杠
    $paths = array_map(function($path) use ($DIRECTORY_SEPARATOR) {
        $path = str_replace('/', $DIRECTORY_SEPARATOR, $path);
        if ($DIRECTORY_SEPARATOR === '\\') {
            $path = str_replace('\\', '\\\\', $path);
        }
        return $path;
    }, $paths);
    
    // 计算相对路径并返回结果
    return array_map(function($path) use ($root) {
        $result = str_replace($root, '', $path);
        return ($result == '') ? '/' : $result;
    }, $paths);
}

?>