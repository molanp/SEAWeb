<?php
class gpt_url {
    public function getInfo() {
        return [
            'name' => 'GPT 镜像',
            'version' => '1.0',
            'profile'=> '一键获取最新可用的gpt镜像网站',
            'method'=>'GET',
            'author'=>'molanp',
            'request_par'=> re_par(['-'=>'-']),
            'return_par'=> re_par([
                'count' => 'gpt镜像url数量',
                'default' => '可用gpt镜像url列表'
            ]),
            //...more
        ];
    }

    public function run() {
        $default = [];
        $html = curl_get('https://c.aalib.net/tool/chatgpt/');
        preg_match_all('/<td><a\s+href="([^"]+)"\s+target="_blank">([^<]+)<\/a><\/td>/', $html, $matches);
        if (count($matches[1]) > 0) {
            foreach ($matches[1] as $index => $link) {
                $default[] = $link; // 将满足条件的链接加入数组
            }
        }
        $html = curl_get('http://doc.wuguokai.cn/s/xPq1iNw_v');
        preg_match_all('/国内加速站点\d+：\s*(https?:\/\/[^\s]+)/', $html, $matches);
        if (count($matches[1]) > 0) {
            foreach ($matches[1] as $index => $link) {
                $default[] = $link;
            }
        }
        _return_(['count'=>count($default),'default'=>array_values(array_unique($default))]);
    }
}

?>