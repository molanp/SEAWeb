<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/configs/config.php");
include_once("watchdog.php");
include_once("logger.php");

if ($bind) {
    $DATABASE = new PDO($bind, $mysql_username, $mysql_password);
} else {
    throw new Exception("数据库配置未填写，请前往configs/config.php填写！");
};
$DATABASE->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function UpdateOrCreate($pdo, $table, $data)
{
    $columnNames = implode(", ", array_keys($data));
    $placeholders = ":" . implode(", :", array_keys($data));

    $insertSql = "INSERT INTO {$table} ({$columnNames}) VALUES ({$placeholders}) ";
    $updateSql = "";

    foreach ($data as $key => $value) {
        $updateSql .= "{$key} = :{$key}, ";
    }
    $updateSql = rtrim($updateSql, ", ");

    $insertSql .= "ON DUPLICATE KEY UPDATE {$updateSql}";

    $stmt = $pdo->prepare($insertSql);
    $stmt->execute($data);
}


function tokentime($token=123456) {
    global $DATABASE;

    $token = $token ?: 123456;
    // 检查令牌是否存在于数据库中
    $stmt = $DATABASE->query("SELECT logtime, username FROM users WHERE token = '$token'");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // 检查令牌是否在30分钟内
        if (time() - $row['logtime'] < 60 * 30) {
            return true;
        } else {
            return false;
        }
    } else {
        // 检查令牌是否存在作为API密钥
        $stmt = $DATABASE->query("SELECT username FROM users WHERE apikey = '$token'");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return true;
        } else {
            return false;
        }
    }
}

function apineedupdate() {
    global $DATABASE;

    // 查询是否存在api表并且获取最后更新时间
    $stmt = $DATABASE->query("SELECT MAX(time) FROM api");
    $lastUpdateTime = $stmt->fetchColumn();

    if ($lastUpdateTime !== false) {
        // 检查最后更新时间是否在30分钟内
        if (time() - $lastUpdateTime < 60 * 30) {
            return false;
        }
    }

    // 如果没有api表或者最后更新时间超过了30分钟，则需要更新
    return true;
}
