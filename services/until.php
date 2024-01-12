<?php
ini_set('date.timezone', 'Asia/Shanghai');
include_once('Config.class.php');
include_once('requests.php');
include_once('watchdog.php');

/**
 * 在给定的字符串开头或末尾添加斜杠("/")，如果它尚未以斜杠结尾。
 *
 * @param string $inputString 要处理的字符串
 * @return string 处理后的字符串，确保以斜杠结尾
 */
function addSlashIfNeeded($inputString)
{
    if (substr($inputString, 0, 1) !== '/') {
        $inputString = '/' . $inputString;
    }
    if (substr($inputString, -1) !== '/') {
        $inputString .= '/';
    }
    return $inputString;
}
/**
 * 检查给定文件夹中是否存在指定的文件
 *
 * @param array $folders 文件夹路径的数组
 * @param string $file 要检查的文件名
 * @return array|bool 如果文件存在，则返回包含文件路径的数组；如果文件不存在，则返回false
 */
function check_files($floders, $file)
{
    $files = [];
    foreach ($floders as $floder) {
        if (file_exists($floder . $file)) {
            $files[] = $floder . $file;
        }
    }
    if (count($files) > 0) {
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
function re_add($type = [], $url = [])
{
    $table = "|Method|URL|Info|\n|---|---|---|";
    $info = array_values($url);
    $url = array_keys($url);
    for ($i = 0; $i < count($type); $i++) {
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
function re_par($key = [])
{
    $table = "| Key | Info |\n| --- | --- |";
    parseTable('', $key, $table);
    return $table;
}

function parseTable($prefix, $key, &$table)
{
    foreach ($key as $k => $v) {
        if (is_array($v)) {
            parseTable($prefix . "{$k}.", $v, $table);
        } else {
            $table .= "\n| `{$prefix}{$k}` | {$v} |";
        }
    }
}

/**
 * 返回结果
 *
 * @param mixed $content 返回的数据内容
 * @param int $status 状态码，默认为 200
 * @param bool|string $location 是否重定向，如果不为false，则重定向到$content内的链接
 * @return void
 */
function _return_($content, $status = 200, $location = false)
{
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: *');
    header('Access-Control-Allow-Headers: *');
    header('Access-Control-Expose-Headers: *');
    header('Access-Control-Max-Age: 3600');
    header("HTTP/1.1 $status");
    if ($location === false) {
        header('Content-type:text/json;charset=utf-8');
        die(json_encode(['status' => $status, 'data' => $content, 'time' => time()], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    } else {
        die(header("Location: $content"));
    }
}
/**
 * 检查 API 状态
 *
 * @return bool 返回是否需要处理 API 请求
 */
function handle_check($name)
{
    global $DATABASE;
    include_once("connect.php");
    $query = $DATABASE->prepare("SELECT status FROM api WHERE name = ?");
    $query->bindParam(1, $name, PDO::PARAM_STR);
    $query->execute();
    $status = $query->fetchColumn();
    if ($status == 'false' || $DATABASE->query("SELECT value FROM setting WHERE item = 'maintenance_mode'")->fetchColumn() == 'true') {
        _return_("This API already closed", 406);
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
function find_files($dirs, $file = '.php', $prefix = '')
{
    $relative_paths = [];

    foreach ($dirs as $dir) {
        if (is_dir($dir)) {
            $subdirs = glob("$dir/*", GLOB_ONLYDIR);
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
 * 尝试将 JSON 字符串解析为数组，或将数组转换为 JSON 字符串
 *
 * @param string|array $json 要解析的 JSON 字符串或要转换的数组
 * @return array|string 返回解析后的数组或数组转换后的 JSON 字符串，如果解码和编码都失败则返回原始字符串
 */
function json($json)
{
    if (is_array($json)) {
        $encoded = json_encode((object)$json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
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
 * 此函数应在 run() 内执行
 *
 * @param string $limit 最大请求次数和时间单位，例如 '3/10s', '10/min', '5/hour', '100/day'
 * @return void
 */
function RequestLimit($limit)
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? "Unknown";
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

    $startTime = $currentTime - $interval;

    include_once("connect.php");
    global $DATABASE;

    $url = addSlashIfNeeded(parse_url($_SERVER['REQUEST_URI'])["path"]) ?? "Unknown";
    $stmt = $DATABASE->prepare("SELECT COUNT(*) AS count FROM access_log WHERE ip = :ip AND time >= :start_time AND time <= :end_time AND url = :url;");
    $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
    $stmt->bindValue(':start_time', date('Y-m-d H:i:s', $startTime), PDO::PARAM_INT);
    $stmt->bindValue(':end_time', date('Y-m-d H:i:s', $currentTime), PDO::PARAM_INT);
    $stmt->bindParam(':url', $url);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $count = $result['count'];

    $count = $count + 1;
    if ($count > $quantity && $ip !== "127.0.0.1" && $ip !== "::1") {
        _return_('请求次数超过限制(Too Many Requests)', 429);
    }
}

/**
 * 将请求记录到日志
 * @return null 无返回数据
 */
function req_log()
{
    include_once("connect.php");
    global $DATABASE;
    $ip = $_SERVER["REMOTE_ADDR"] ?? "Unknown";
    $url = addSlashIfNeeded(parse_url($_SERVER['REQUEST_URI'])["path"]) ?? "Unknown";
    $referer = $_SERVER["HTTP_REFERER"] ?? "Unknown";
    $param = json($_REQUEST);
    $stmt = $DATABASE->prepare("INSERT INTO access_log (time, ip, url, referer, param, method, agent) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bindValue(1, date('Y-m-d H:i:s'));
    $stmt->bindParam(2, $ip);
    $stmt->bindParam(3, $url);
    $stmt->bindParam(4, $referer);
    $stmt->bindParam(5, $param);
    $stmt->bindParam(6, $_SERVER["REQUEST_METHOD"]);
    $stmt->bindParam(7, $_SERVER['HTTP_USER_AGENT']);
    $stmt->execute();
}

/**
 * 获取路径数组相对于根路径的路径
 * @param array $paths 路径数组
 * @param string $root 根路径，默认为根目录
 * @return array 相对路径数组
 */
function getPath($paths, $root = NULL)
{
    $DIRECTORY_SEPARATOR = DIRECTORY_SEPARATOR;
    if (empty($root)) {
        $root = $_SERVER['DOCUMENT_ROOT'];
    }

    if (substr($root, -1) != $DIRECTORY_SEPARATOR) {
        $root .= $DIRECTORY_SEPARATOR;
    }

    $root = str_replace('/', $DIRECTORY_SEPARATOR, $root);

    $paths = array_map(function ($path) use ($DIRECTORY_SEPARATOR) {
        $path = str_replace('/', $DIRECTORY_SEPARATOR, $path);
        if ($DIRECTORY_SEPARATOR === '\\') {
            $path = str_replace('\\', '\\\\', $path);
        }
        return $path;
    }, $paths);

    return array_map(function ($path) use ($root) {
        $result = str_replace($root, '', $path);
        return ($result == '') ? '/' : $result;
    }, $paths);
}

/**
 * 根据输入的HTTP状态码返回对应的内容
 * @param int $code HTTP状态码
 * @return void 返回JSON格式的内容并结束脚本
 * @throws Exception 如果HTTP状态码不存在则抛出异常
 */
function code($code)
{
    $data = [
        100 => '继续努力喔！',
        101 => '切换协议啦！',
        200 => 'OK啦！',
        201 => '已经创建啦！',
        202 => '接受啦！',
        203 => '权威信息不太权威，但是...',
        204 => '这里什么都没有喔！',
        205 => '内容已经重置啦！',
        206 => '部分内容也很好喔！',
        300 => '好多选择喔！',
        301 => '永久搬家啦！',
        302 => '找到啦！',
        303 => '看看别的地方吧！',
        304 => '内容没有变喔！',
        305 => '需要使用代理喔！',
        307 => '暂时搬家啦！',
        400 => '糟糕！请求有问题喔！',
        401 => '你没有权限喔！',
        402 => '需要付费喔！',
        403 => '禁止访问喔！',
        404 => '迷路了喔！找不到啦！',
        405 => '这个方法不被允许喔！',
        406 => '不接受喔！再试一次吧！',
        407 => '需要代理验证喔！',
        408 => '请求超时啦！休息一下吧！',
        409 => '发生冲突啦！',
        410 => '消失啦！永远不见啦！',
        411 => '需要长度喔！',
        412 => '前提条件失败啦！',
        413 => '请求太大啦！',
        414 => '请求太长啦！',
        415 => '不支持的媒体类型喔！',
        416 => '请求的范围不符合要求喔！',
        417 => '期望失败啦！',
        500 => '服务器出错啦！糟糕！',
        501 => '还没有实现喔！',
        502 => '网关出错啦！稍后再试试吧！',
        503 => '服务暂时不可用喔！稍后再试试吧！',
        504 => '网关超时啦！稍后再试试吧！',
        505 => '不支持的HTTP版本喔！'
    ];    

    if (array_key_exists($code, $data)) {
        _return_($data[$code], $code);
    } else {
        throw new Exception("Unknow HTTp code.");
    }
}
