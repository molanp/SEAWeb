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
        ];
    }

    public function run() {
        $default = [];
        $html = requests->get('https://c.aalib.net/tool/chatgpt/')->json();
        preg_match_all('/<td><a\s+href="([^"]+)"\s+target="_blank">([^<]+)<\/a><\/td>/', $html, $matches);
        if (count($matches[1]) > 0) {
            foreach ($matches[1] as $index => $link) {
                $default[] = $link;
            }
        }
        _return_(['count'=>count($default),'default'=>array_values(array_unique($default))]);
    }
}

?>