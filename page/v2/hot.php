<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/services/until.php");
req_log();
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    include_once($_SERVER["DOCUMENT_ROOT"] . "/services/connect.php");
    $data = [];
    if (tokentime($_GET)) {
        $result = $DATABASE->query("SELECT DATE(time) AS date, COUNT(*) AS count FROM access_log WHERE url LIKE '/api%' GROUP BY DATE(time) ORDER BY date ASC LIMIT 5");
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            array_push($data, ["date" => $row["date"], "count" => $row["count"]]);
        }        
    } else {
        $countsStmt = $DATABASE->prepare("SELECT access_log.url, COUNT(*) AS count, api.name FROM access_log JOIN api ON SUBSTRING(access_log.url, 5) = api.url_path WHERE access_log.url LIKE '/api%' GROUP BY access_log.url, api.name ORDER BY count DESC LIMIT 10");
        $countsStmt->execute();
        $data = $countsStmt->fetchAll(PDO::FETCH_ASSOC);
    }
    _return_($data);
}
