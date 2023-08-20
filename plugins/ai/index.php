<?php
class ai {
    public function getInfo() {
        return [
            'name' => 'AI回复',
            'version' => '1.0',
            'profile'=> '搭载青云客ai与本地词库，相对智能的ai.与ai普普通通的对话吧！_~~或许词库有点二次元?~~_',
            'method'=>'GET',
            'author'=>'molanp',
            'request_par'=> re_par(['*msg'=>'你要对ai说的话']),
            'return_par'=> re_par(['data'=>'ai给你的回复']),
        ];
    }
    private function findMostSimilarWord($input, $dictionary) {
        $bestMatch = '';
        $bestScore = 0;
        foreach ($dictionary as $word) {
            $score = similar_text($input, $word);
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $word;
            }
        }
        if ($bestScore >= 0.5) {
            return $bestMatch;
        } else {
            return NULL;
        }
    }

    public function run($get) {
        $anime = json(file_get_contents(__DIR__.'/anime.json'));
        $wozai = [
            "哦豁？！",
            "你好！Ov<",
            "库库库，呼唤咱做什么呢",
            "我在呢！",
            "呼呼，叫俺干嘛",
        ];
        $budong = [
            "你在说啥子？",
            "纯洁的咱没听懂",
            "下次再告诉你(下次一定)",
            "你觉得我听懂了吗？嗯？",
            "我！不！知！道！",
        ];
        $msg = $get['msg'] ?? '都不知道说什么';
        foreach(["你好啊","你好","在吗","在不在","您好","您好啊","你好","在",] as $zai){
            if (strpos($msg,$zai)!==false) {
                $data = $wozai[array_rand($wozai)];
                break;
            } else {$data = NULL;}
        }
        if (empty($data)) {
            $words = $anime[$this->findMostSimilarWord($msg,array_keys($anime))] ?? NULL;
            if ($words) {
                $data = $words[array_rand($words)];
            }
        }
        if (empty($data)) {
            $result = requests->get("http://api.qingyunke.com/api.php",["key"=>"free","appid"=>0,"msg"=>$msg])->json();
            if ($result["result"]==0) {
                $data = $result["content"];
                str_ireplace("菲菲","咱",$data);
                str_ireplace("艳儿","咱",$data);
                str_ireplace("{br}","\n",$data);
                if(strpos($data,"提示")!==false){$data=mb_substr($data,0,strpos($data,"提示"),"utf-8");}
                if(strpos($data,"公众号")!==false){$data=NULL;}
                if(strpos($data,"taobao.com")!==false){$data=NULL;}
                if(strpos($data,"淘宝")!==false){$data=NULL;}
            }
        }
        if (empty($data)) {
            $data = $budong[array_rand($budong)];
        }
        _return_($data, 200);
    }
}