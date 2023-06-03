<?php
include_once('Config.class.php');

//http解释
$httpStatus = [
    100 => 'Continue',
    101 => 'Switching Protocols',
    102 => 'Processing',
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    207 => 'Multi-Status',
    208 => 'Already Reported',
    226 => 'IM Used',
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found',
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    307 => 'Temporary Redirect',
    308 => 'Permanent Redirect',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Timeout',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Payload Too Large',
    414 => 'URI Too Long',
    415 => 'Unsupported Media Type',
    416 => 'Range Not Satisfiable',
    417 => 'Expectation Failed',
    418 => 'I\'m a teapot',
    421 => 'Misdirected Request',
    422 => 'Unprocessable Entity',
    423 => 'Locked',
    424 => 'Failed Dependency',
    426 => 'Upgrade Required',
    428 => 'Precondition Required',
    429 => 'Too Many Requests',
    431 => 'Request Header Fields Too Large',
    451 => 'Unavailable For Legal Reasons',
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    504 => 'Gateway Timeout',
    505 => 'HTTP Version Not Supported',
    506 => 'Variant Also Negotiates',
    507 => 'Insufficient Storage',
    508 => 'Loop Detected',
    510 => 'Not Extended',
    511 => 'Network Authentication Required'];
//随机ip
function randip(){
    $ip_1 = -1;
    $ip_2 = -1;
    $ip_3 = rand(0,255);
    $ip_4 = rand(0,255);
    $ipall = array(
                    array(array(58,14),array(58,25)),
                    array(array(58,30),array(58,63)),
                    array(array(58,66),array(58,67)),
                    array(array(60,200),array(60,204)),
                    array(array(60,160),array(60,191)),
                    array(array(60,208),array(60,223)),
                    array(array(117,48),array(117,51)),
                    array(array(117,57),array(117,57)),
                    array(array(121,8),array(121,29)),
                    array(array(121,192),array(121,199)),
                    array(array(123,144),array(123,149)),
                    array(array(124,112),array(124,119)),
                    array(array(125,64),array(125,98)),
                    array(array(222,128),array(222,143)),
                    array(array(222,160),array(222,163)),
                    array(array(220,248),array(220,252)),
                    array(array(211,163),array(211,163)),
                    array(array(210,21),array(210,22)),
                    array(array(125,32),array(125,47))     
    );
    $ip_p = rand(0,count($ipall)-1);#随机生成需要IP段
    $ip_1 = $ipall[$ip_p][0][0];
    if($ipall[$ip_p][0][1] == $ipall[$ip_p][1][1]){
        $ip_2 = $ipall[$ip_p][0][1];
    }else{
        $ip_2 = rand(intval($ipall[$ip_p][0][1]),intval($ipall[$ip_p][1][1]));
    }
    $member = null;
    $ipall  = null;
    return $ip_1.'.'.$ip_2.'.'.$ip_3.'.'.$ip_4;
}

//组合table
function re_add($type=[],$url=[],$info=[]) {
    $table = '|Method|URL|Info|
    |---|---|---|';
    for($i=0; $i<count($type); $i++) {
        $table .= "\n|{$type[$i]}|[{$url[$i]}]({$url[$i]})|{$info[$i]}|";
    };
    return $table;
}
function re_par($key=[],$info=[]) {
    $table = '|Key|Info|
    |---|---|';
    for($i=0; $i<count($key); $i++) {
        $table .= "\n|`{$key[$i]}`|{$info[$i]}|";
    };
    return $table;
}
//curl_get获取数据
function curl_get($url,$data=[],$code=false){
    if($url == "" ){
        return false;
    }
    $url = $url.'?'.http_build_query($data);
    $ch = curl_init();
    $ip = randip();
    $user_agents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
        'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:54.0) Gecko/20100101 Firefox/54.0',
        'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
        'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; AS; rv:11.0) like Gecko',
        'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:54.0) Gecko/20100101 Firefox/54.0'];
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true) ;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER,0);
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agents[array_rand($user_agents)]);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept-Language: en-US,en;q=0.8',
        'Upgrade-Insecure-Requests: 1',
        'Cache-Control: no-cache']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-Forwarded-For: ' . $ip,
        'Client-Ip: ' . $ip]);
    curl_setopt($ch,CURLOPT_TIMEOUT,5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $output = curl_exec($ch);
    curl_close($ch);
    if(curl_exec($ch) === false){
        return curl_error($ch);
    }
    if($code === true){
        return curl_getinfo($ch, CURLINFO_HTTP_CODE);
    }
    try {
        return json_decode($output,true);
    } catch (\Exception $error) {
        return (string) $output;
    } catch (\Error $error) {
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
        $DATA = new Config($_SERVER['DOCUMENT_ROOT'].'/db/status');
        $status=$DATA->get($api_name,true);
        if ($status !== true) {
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
    $DATA = new Config($_SERVER['DOCUMENT_ROOT'].'/db/db');
    if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/db')) {
        mkdir($_SERVER['DOCUMENT_ROOT'].'/db');
        $DATA->set("account",["username"=>"admin","password"=>hash('sha256', 'password')])->save();
        $DATA->set("web",[
            "record"=>"",
            "index_title"=>"SEAWeb",
            "copyright"=>"",
            "index_description"=>"这是网站简介，这里支持*MarkDown*语法",
            "notice"=>[
                "data"=>"> **这里也支持markdown语法**\n欢迎使用SEAWeb，本模板由[molanp](https://github.com/molanp)开发与维护。目前正在不断完善~\n如果你觉得这个API有什么不完善的地方或者说你有什么更好的想♂法，可以在[issues](https://github.com/molanp/easyapi_wesbite/issues)上提出建议",
                "latesttime"=>date('Y-m-d')],
            "keywords"=>"API,api",
            "links"=>"[GitHub](https://github.com/molanp/SEAWeb)\n[Issues](https://github.com/molanp/SEAWeb/issues)\n[开发指南](https://molanp.github.io/SEAWeb)"])->save();
    }
}
?>