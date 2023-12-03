<?php
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
    if (isset($errfile,$errline)) {
        $message = "[$errno]: $errstr in $errfile line $errline.";
    } elseif(isset($errstr)) {
        $message = "[$errno]: $errstr";
    } else {
        $message = "$errno";
    };
    include_once("logger.php");
    (new logger())->error($message);
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Expose-Headers: *");
    header("Access-Control-Max-Age: 3600");
    header("Content-type:text/json;charset=utf-8");
    die(json_encode(["status"=>500,"data"=>$message,"time"=>time()],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
}
//set_error_handler("watchdog");
set_exception_handler("watchdog");