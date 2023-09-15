<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');
    include_once($_SERVER['DOCUMENT_ROOT'].'/services/connect.php');
    if(tokentime($_GET["apikey"]??123456)) {
        //if ($_POST[])
        $result = $database->query("SELECT SUBSTR(time, 1, 10) AS date, COUNT(*) AS count FROM access_log GROUP BY SUBSTR(time, 1, 10) ORDER BY date ASC LIMIT 5");
        $data = [];
    
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $date = $row['date'];
            $count = $row['count'];
            array_push($data, ["date"=>$date, "count"=>$count]);
        }
    } else {
        $result = $database->query("SELECT name, url, COUNT(*) AS count FROM access_log GROUP BY name, url ORDER BY count DESC LIMIT 10");
        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = [
                'name' => $row['name'],
                'url' => $row['url'],
                'count' => $row['count']
            ];
        };
    }
    _return_($data);
}