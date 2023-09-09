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
    _return_($message,500);
}
set_error_handler("watchdog");
set_exception_handler("watchdog");