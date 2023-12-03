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


function tokentime($data) {
    global $DATABASE;

    $token = $data["token"] ?? $data["apikey"] ?? 123456;
    $stmt = $DATABASE->query("SELECT logtime, username FROM users WHERE token = '$token'");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        if (time() - $row["logtime"] < 60 * 30) {
            return true;
        } else {
            return false;
        }
    } else {
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

    $stmt = $DATABASE->query("SELECT MAX(time) FROM api");
    $lastUpdateTime = $stmt->fetchColumn();

    if ($lastUpdateTime !== false) {
        if (time() - $lastUpdateTime < 60 * 30) {
            return false;
        }
    }

    return true;
}
