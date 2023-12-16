<?php
class zhihu_yx {
    function getInfo() {
        return [
            "name" => "知乎盐选",
            "version" => "1.0",
            "profile" => "一键获取知乎盐选文章<br>文章版权归原作者所有<br>__仅供学习交流，严禁违法用途__",
            "method" => "GET",
            "type" => "第三方接口",
            "author" => "molanp",
            "request_par" => re_par(["*url" => "知乎盐选文章地址,若未查询到文章，再次请求即可"]),
            "return_par" => re_par([
                "id" => "文章id",
                "title" => "文章标题",
                "content" => "文章内容",
                "createTime" => "文章保存时间",
            ])
        ];
    }
    private function fetchArticle($url) {     
        RequestLimit("20/min");
        $database = __DIR__."/zhihu.db";
        $api = "http://36.134.102.174:8087/article/link";
        $parsedUrl = parse_url($url);
        $url = $parsedUrl["scheme"] . "://" . $parsedUrl["host"] . $parsedUrl["path"];
        if (!preg_match("/^(https?:\/\/)?(www\.)?zhihu\.com\/market\/paid_column\/\d+\/section\/\d+$/", $url)) {
            return [
                "code" => 400,
                "data" => "Invalid URL format.Only support http(s)://(www.)zhihu.com/market/paid_column/d+/section/d+ but ".$url." given."
            ];
        }
        if (!file_exists($database)) {
            $db = new SQLite3($database);
            $db->exec("CREATE TABLE IF NOT EXISTS articles (id INTEGER, title TEXT, content TEXT, createTime TEXT, url TEXT)");
            $db->close();
        }
        $db = new SQLite3($database);
        $stmt = $db->prepare("SELECT id, title, content, createTime FROM articles WHERE url = :url");
        $stmt->bindValue(":url", $url);
        $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        if ($result) {
            $db->close();
            return [
                "code" => 304,
                "data" => [
                    "id" => $result["id"],
                    "title" => $result["title"],
                    "content" => $result["content"],
                    "createTime" => $result["createTime"]
                ]
            ];
        }
        $token = $GLOBALS["requests"]->post("http://36.134.102.174:8087/user/login",["userName"=>"seaweb","password"=>"123456"])->header([
            "Host"=>"36.134.102.174:8087",
            "Origin"=>"http://119.91.35.62",
            "Referer"=>"http://119.91.35.62/"//请勿高频请求接口，爱护接口。增加使用寿命
        ])->json();
        if ($token && isset($token["code"]) && $token["code"] === 200) {
            $token = $token["data"]["zltoken"];
        } else {
            return [
                "code" => 503,
                "data" => ["msg"=>"Failed to get token.","reason"=>$token]
            ];
        }
        $data = $GLOBALS["requests"]->get($api,["url"=>$url])->header([
            "Host"=>"36.134.102.174:8087",
            "Origin"=>"http://119.91.35.62",
            "Referer"=>"http://119.91.35.62/",
            "zltoken"=>$token
        ])->json();
        if ($data && isset($data["code"]) && $data["code"] == 200 && $data["data"]["title"] != "无此文章") {
            $title = trim(preg_replace("/(\s+)?(第)?(\s+)?\d+\s+节\s+/u","",$data["data"]["title"]));
            $content = $data["data"]["content"];
            $content = str_ireplace("　　","",$content);
            $content = str_ireplace("\n ","\n",$content);
            $content = preg_replace("/(\s*)?第\s*\d+\s*节\s*$title/", "", $content);
            $content = str_replace("</p>", "\n", strip_tags($content));
            $content = preg_replace("/\n\s*\n\n\n(\s*)?/","",$content);
            if ($content=="") {
                return [
                    "code" => 400,
                    "data" => "Failed to fetch article."
                ];
            } elseif (stripos($content, "\n \n \n \n ") !== false) {
                return [
                    "code" => 400,
                    "data" => "Failed to fetch article."
                ];
            }
            $maxIdResult = $db->querySingle("SELECT MAX(id) FROM articles");
            $maxId = $maxIdResult !== false ? (int) $maxIdResult : 0;
            $newId = $maxId + 1;
            $createTime = date("Y-m-d H:i:s");
            $stmt = $db->prepare("INSERT INTO articles (id, title, content, createTime, url) VALUES (:id, :title, :content, :createTime, :url)");
            $stmt->bindValue(":id", $newId);
            $stmt->bindValue(":title", $title);
            $stmt->bindValue(":content", $content);
            $stmt->bindValue(":createTime", $createTime);
            $stmt->bindValue(":url", $url);
            $stmt->execute();
            $db->close();
            return [
                "code" => 200,
                "data" => [
                    "id" => $newId,
                    "title" => $title,
                    "content" => $content,
                    "createTime" => $createTime
                ]
            ];
        } else {
            if (isset($data["data"])) {
                return [
                    "code" => $data["code"],
                    "data" => $data["data"]["title"]
                ];
            } elseif(isset($data["msg"])) {
                return [
                    "code" => $data["code"],
                    "data" => $data["msg"]
                ];
            } else {
                return [
                    "code" => 500,
                    "data" => $data
                ];
            }
        }
    }
    function run($get) {
        $url = $get["url"] ?? "http://NO URL/";
        $result = $this->fetchArticle($url);
        _return_($result["data"],$result["code"]);
    }
}
