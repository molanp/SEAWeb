<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/services/until.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    include_once($_SERVER['DOCUMENT_ROOT'].'/services/connect.php');
    // 假设 $urlPath 是你要查询的 URL 路径
    $urlPath = $_GET["url"];
    
    // 构建查询的 SQL 语句
    $sql = "SELECT name, version, author, method, profile, request, response, type, status FROM api WHERE url_path = :urlPath";
    
    // 使用预处理语句执行查询
    $statement = $database->prepare($sql);
    $statement->execute([":urlPath" => $urlPath]);
    
    // 获取查询结果
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    
    // 创建一个关联数组，以列名为键
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
        $stmt = $database->prepare($query);
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
    