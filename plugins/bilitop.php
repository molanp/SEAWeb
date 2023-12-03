<?php
class bilitop
{
    public function getInfo()
    {
        return [
            'name' => 'B站热门视频',
            'version' => '1.0',
            'profile' => '一键B站热门视频',
            'method' => 'GET',
            'author' => 'molanp',
            'request_par' => re_par(),
            'return_par' => re_par([
                "data" => [
                    "name" => "视频名称",
                    "url" => "视频链接"
                ]
            ])
        ];
    }
    public function run()
    {
        $bilitop = $GLOBALS["requests"]->get("https://api.bilibili.com/x/web-interface/popular")->json()["data"]["list"];
        $i = 0;
        $top = [];
        for ($i; $i < count($bilitop); $i++) {
            if (isset($bilitop[$i]["title"])) {
                $top[] = [
                    "rank" => count($top) + 1,
                    "name" => $bilitop[$i]["title"],
                    "url" => $bilitop[$i]["short_link_v2"]
                ];
            }
        };
        _return_($top);
    }
}
