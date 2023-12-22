<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/services/until.php");
req_log();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once($_SERVER["DOCUMENT_ROOT"]."/services/Config.class.php");
    include_once($_SERVER["DOCUMENT_ROOT"]."/services/connect.php");

    $for = $_POST["for"] ?? NULL;
    $token = $_POST;
    switch($for) {
        case "web":
            if (tokentime($token)) {
                $WEB= new Data();
                $WEB->set("web",[
                    "record"=>$_POST["record"],
                    "index_title"=>$_POST["index_title"],
                    "copyright"=>$_POST["copyright"],
                    "index_description"=>$_POST["index_description"],
                    "notice"=>$_POST["notice"],
                    "keywords"=>$_POST["keywords"],
                    "links"=>$_POST["links"]]);
                code(200);
            } else {
                code(401);
            }
            break;
        case "setting":
            if (tokentime($token)) {
                foreach ($_POST["data"] as $key => $value) {
                    foreach($value as $key => $value) {
                        $DATABASE->exec("UPDATE setting SET value='$value' WHERE item='$key'");
                    }
                };
                code(200);
            } else {
                code(401);
            }
            break;
        case "status":
            if (tokentime($token)) {
                $keys = array_keys($_POST["data"]);
                $data = array_values($_POST["data"]);
                $i = 0;
                for ($i;$i<count($data);$i++) {
                    $DATABASE->exec("UPDATE api SET status='$data[$i]' WHERE name='$keys[$i]'");
                }
                code(200);
            } else {
                code(401);
            }
            break;
        }
}