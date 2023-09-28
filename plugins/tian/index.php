<?php
class tian {
    public function getInfo() {
        return [
            'name' => '舔狗日记',
            'version' => '20230929',
            'profile'=> '一键获取舔狗日记',
            'method'=>'GET',
            'author'=>'molanp',
            'request_par'=> re_par(),
            'return_par'=> re_par(['data'=>'舔狗语录']),
        ];
    }
    public function run() {
        $data = json(file_get_contents(__DIR__.'/tgrj.json'))["data"];
        _return_($data[array_rand($data)]);
    }
}