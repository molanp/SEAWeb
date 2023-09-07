<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include_once($_SERVER['DOCUMENT_ROOT'].'/services/Config.class.php');
    include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');
    include_once($_SERVER['DOCUMENT_ROOT'].'/services/connect.php');

    $for = $_POST['for'] ?? NULL;
    $token = $_POST['apikey'] ?? 123456;
    switch($for) {
        case 'web':
            if (tokentime($token)) {
                $WEB= new Config($_SERVER['DOCUMENT_ROOT'].'/data/web');
                $WEB->set("web",[
                    "record"=>$_POST["record"],
                    "index_title"=>$_POST["index_title"],
                    "copyright"=>$_POST["copyright"],
                    "index_description"=>$_POST["index_description"],
                    "notice"=>[
                        "data"=>$_POST["notice"],
                        "latesttime"=>date('Y-m-d')],
                    "keywords"=>$_POST["keywords"],
                    "links"=>$_POST["links"]])->save();

                _return_("修改成功");
            } else {
                _return_("身份验证失败",403);
            }
            break;
        case 'setting':
            if (tokentime($token)) {
                foreach ($_POST["data"] as $key => $value) {
                    foreach($value as $key => $value) {
                        $database->exec("UPDATE setting SET value='".strval($value)."' WHERE item='".$key."'");
                    }
                };
                _return_("修改成功");
            } else {
                _return_("身份验证失败",403);
            }
            break;
        case 'status':
            if (tokentime($token)) {
                $keys = array_keys($_POST["data"]);
                $data = array_values($_POST["data"]);
                $i = 0;
                for ($i;$i<count($data);$i++) {
                    $database->exec("UPDATE api SET status='".$data[$i]."' WHERE name='".$keys[$i]."'");
                }
                _return_("修改成功");
            } else {
                _return_("身份验证失败",403);
            }
            break;
        }
}