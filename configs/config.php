<?php
    $sqlite_mode = None;
    //是否使用sqlite本地储存模式，为true自动忽略以下项，本地空间小于10MB慎用此项
    $bind = "";
    //其他数据库的链接bind，如mysql:host=127.0.0.1;port=5555;dbname=mydatabase', 'username', 'password'
    //暂时仅支持mysql,其他数据库未测试