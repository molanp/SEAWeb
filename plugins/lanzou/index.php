<?php
class lanzou {
    public function getInfo() {
        return [
            'name' => '蓝奏云解析',
            'version' => '1.0',
            'profile'=> '在线解析蓝奏云下载链接，获取文件直链',
            'method'=>'GET',
            'author'=>'molanp',
            'request_par'=> re_par([
                '*url' => '蓝奏云文件分享链接',
                'pwd' => '文件的下载密码(无密码请留空)',
                'down' => '是否直接跳转下载，是则填`true`'
            ]),
            'return_par'=> re_par([
                'name' => '文件名称',
                'filesize' => '文件大小',
                'url' => '文件下载地址'
            ]),
        ];
    }
    public function run($get) {
        include_once('func.php');
        $url = $get['url'] ?? NULL;
        $pwd = $get['pwd'] ?? NULL;
        $down = $get['down'] ?? NULL;
        $result = lanzou($url,$pwd);
        if ($down == true && $result["code"] == 200) {
            _return_($result["msg"]["url"],200,true);
        }
        _return_($result["msg"],$result["code"]);
    }
}
?>