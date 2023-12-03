<?php
class mrfd {
    public function getInfo() {
        return [
            "name" => "每日发癫语录",
            "version" => "1.0",
            "profile"=> "每日一句发癫语录",
            "method"=>"GET",
            "author"=>"molanp",
            "request_par"=> re_par(["*name"=>"需要发癫的对象"]),
            "return_par"=> re_par()
        ];
    }
    public function run($r) {
        $d = json(file_get_contents(__DIR__."/data.json"))["main"];
        if (!isset($r["name"])) {
            $result = "对空气发癫？";
        } else {
            $result = str_replace("<name>", $r["name"], 
                $d[array_rand($d)]
            );
        };
        _return_($result);
    }
}