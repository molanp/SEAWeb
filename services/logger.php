<?php
ini_set('date.timezone','Asia/Shanghai');
class logger {

    private $file;
    private $error_file;

    function __construct() {
        if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/logs')) {
            mkdir($_SERVER['DOCUMENT_ROOT'].'/logs');
        };
        $file = $_SERVER['DOCUMENT_ROOT'].'/logs/'.date("Y-m-d").'.log'; 
        $error_file = $_SERVER['DOCUMENT_ROOT'].'/logs/'."error_".date("Y-m-d").'.log'; 
        $this->error_file = $error_file;
        $this->file = $file;
    }

    function info($str) {
        $file = fopen($this->file, 'a'); 
        if(!$file) return '写入文件失败，请赋予 '.$file.' 文件写权限！'; 
        $str = date("Y-m-d H:i:s")."[INFO] > $str\n";
        fwrite($file, $str); 
        fclose($file); 
    }

    function warn($str) {
        $file = fopen($this->file, 'a'); 
        if(!$file) return '写入文件失败，请赋予 '.$file.' 文件写权限！'; 
        $str = date("Y-m-d H:i:s")."[WARN] > $str\n";
        fwrite($file, $str); 
        fclose($file); 
    }

    function debug($str) {
        $file = fopen($this->file, 'a'); 
        if(!$file) return '写入文件失败，请赋予 '.$file.' 文件写权限！'; 
        $str = date("Y-m-d H:i:s")."[DEBUG] > $str\n";
        fwrite($file, $str); 
        fclose($file); 
    }

    function error($str) {
        $file = fopen($this->error_file, 'a'); 
        if(!$file) return '写入文件失败，请赋予 '.$file.' 文件写权限！'; 
        $str = date("Y-m-d H:i:s")."[ERROR] > $str\n";
        fwrite($file, $str); 
        fclose($file); 
    }
}
