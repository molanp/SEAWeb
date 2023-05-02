<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    include_once('../services/Config.class.php');
    include_once('../services/until.php');

    load();
    $DATA = new Config($_SERVER['DOCUMENT_ROOT'].'/db/status');
    $WEB= new Config($_SERVER['DOCUMENT_ROOT'].'/db/db');

    $dir = '../api'; // 文件夹路径
    $relative_paths = find_files($dir);

    $for = $_GET['for'] ?? NULL;
    switch($for) {
        case 'web':
            $WEB = $WEB->get('web');
            $conname = [
                "record"=>$WEB['record'],
                "index_web_name"=>$WEB['index_web_name'],
                "index_title"=>$WEB['index_title'],
                "copyright"=>$WEB['copyright'],
                "index_description"=>$WEB['index_description'],
                "notice"=>$WEB['notice'],
                "keywords"=>$WEB['keywords'],
                "links"=>$WEB['links']
            ];
            break;
        case 'status':
            foreach($relative_paths as $v){
                include_once("../api/".$v);
                $conname[$api_name] = [
                    'status'=>$DATA->get($api_name,'true')
                ];
            }
            break;
        default:
            foreach($relative_paths as $v){
                include_once("../api/".$v);
                preg_match('/^(.*)\/index\.php$/', $v, $v);
                $type = $type ?? '一些工具';
                $conname[$type][$api_name] = [
                    'path'=>$v[1],
                    'api_profile'=>trim($api_profile),
                    'api_address'=>$api_address,
                    'version'=>$version,
                    'author'=>$author,
                    'request_parameters'=>trim($request_par),
                    'return_parameters'=>trim($return_par),
                    'status'=>$DATA->get($api_name,'true')
                ];
                empty($DATA->get($api_name))&&$DATA->set($api_name,'true')->save();
            }
            $api_name = $api_profile = $api_address = $version = $author = $request_parameters = $return_parameters = $type = $status = 'None';
            break;
    }
    _return_($conname);
}
?>