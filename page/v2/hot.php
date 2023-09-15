<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');
    include_once($_SERVER['DOCUMENT_ROOT'].'/services/connect.php');

    $result = $database->query("SELECT name, url, COUNT(*) AS count FROM access_log GROUP BY name, url ORDER BY count DESC LIMIT 10");
    $data = [];

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $data[] = [
            'name' => $row['name'],
            'url' => $row['url'],
            'count' => $row['count']
        ];
    };
    _return_($data);
}