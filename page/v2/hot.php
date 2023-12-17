<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/until.php");
req_log();
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    include_once($_SERVER["DOCUMENT_ROOT"] . "/services/connect.php");
    $data = [];
    if (tokentime($_GET)) {
        $result = $DATABASE->query("SELECT SUBSTR(time, 1, 10) AS date, COUNT(*) AS count FROM access_log WHERE url LIKE '/api%' GROUP BY SUBSTR(time, 1, 10) ORDER BY date ASC LIMIT 5");
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            array_push($data, ["date" => $row["date"], "count" => $row["count"]]);
        }
    } else {
        $counts = $DATABASE->query("SELECT url, COUNT(*) AS count FROM access_log WHERE url LIKE '/api%' GROUP BY url ORDER BY count DESC LIMIT 10");
        while ($row = $counts->fetch(PDO::FETCH_ASSOC)) {
            $url = substr($row['url'], 4);
            $count = $row['count'];
            $apiStmt = $DATABASE->query("SELECT name FROM api WHERE url_path = '$url'");
            $result = $apiStmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $data[] = ["name" => $result['name'], "count" => $count, "url" => $row['url']];
            }
        };
    }
    _return_($data);
}
