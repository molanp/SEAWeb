<?php
header("Access-Control-Allow-Origin: *"); // 允许跨域请求
header("Access-Control-Allow-Methods: POST,GET,OPTIONS,DELETE,PUT"); // 允许全部请求类型
header("Access-Control-Allow-Credentials: true"); // 允许发送 cookies
header("Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin"); // 允许自定义请求头的字段
header("HTTP/1.1 404");
header("Content-type:text/json;charset=utf-8");
die(json_encode(
        [
            "status" => 404,
            "data" => "{$_SERVER['REQUEST_URI']} not Found.",
            "time" => time()
        ],
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    ));
