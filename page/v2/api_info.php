<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    include_once($_SERVER['DOCUMENT_ROOT'].'/services/connect.php');
    $urlPath = $_GET["url"]??"";
    preg_match('#/docs/(.*)#', $urlPath, $urlPath);
    $urlPath = addSlashIfNeeded($urlPath[1]??"");
    $sql = "SELECT name, version, author, method, profile, request, response, type, status FROM api WHERE url_path = :urlPath";
    
    // 使用预处理语句执行查询
    $statement = $DATABASE->prepare($sql);
    $statement->execute([":urlPath" => $urlPath]);
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $data = [];
    
    // 检查是否有匹配的记录
    if ($result) {
        // 将查询结果添加到关联数组
        foreach ($result as $column => $value) {
            $data[$column] = $value;
        };
        //统计调用
        $count = [];
        $query = "SELECT url, COUNT(*) AS count FROM access_log GROUP BY url";
        $stmt = $DATABASE->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count[$row['url']] = $row['count'];
        };
        $data['count'] = $count["/api".$urlPath] ?? 0;
        _return_($data);
    } else {
        // 没有匹配的记录
        _return_("Not Found.", 404);
    }
}
    