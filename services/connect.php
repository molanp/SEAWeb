<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/configs/config.php");
include_once("watchdog.php");
if ($sqlite_mode === true) {
    $database = new PDO("sqlite:".$_SERVER["DOCUMENT_ROOT"]."/data/main.db");
} elseif ($sqlite_mode === false) {
    $database = new PDO($bind);
} else {
    throw new Exception("数据库配置未填写，请前往configs/config.php填写！");
};
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 $database->exec("CREATE TABLE IF NOT EXISTS users(username TEXT, password TEXT, token TEXT, apikey TEXT, permission INTEGER, regtime TEXT, logtime BIGINT)");
 if ($database->query("SELECT COUNT(*) FROM users")->fetchColumn() <= 0) {
    $database->exec("INSERT INTO users(username, password, regtime, permission) VALUES('admin', '".hash('sha256', 'password')."', '".date("Y-m-d H:i:s")."', 9)");
}
$database->exec("CREATE TABLE IF NOT EXISTS setting(item TEXT, value TEXT, info TEXT)");
if ($database->query("SELECT COUNT(*) FROM setting")->fetchColumn() <= 0) {
    $database->exec("INSERT INTO setting(item, value, info) VALUES('maintenance_mode', 'false', '开启后网站将暂停访问')");
}
$database->exec("CREATE TABLE IF NOT EXISTS api(id INTEGER, name TEXT, version TEXT, author TEXT, method TEXT, profile TEXT, request TEXT, response TEXT, class TEXT, url_path TEXT, file_path TEXT, type TEXT, top TEXT, status TEXT, time BIGINT)");
$database->exec("CREATE TABLE IF NOT EXISTS access_log(time TEXT, ip TEXT, url TEXT, referer TEXT, param TEXT)");

function UpdateOrCreate($pdo, $table, $data = [], $condition = []) {
    try {
        $pdo->beginTransaction();

        // 构建SELECT语句
        $selectSql = 'SELECT * FROM ' . $table . ' WHERE ';
        $selectParams = array();
        foreach ($condition as $key => $value) {
            $selectSql .= $key . ' = :' . $key . ' AND ';
            $selectParams[':' . $key] = $value;
        }
        $selectSql = rtrim($selectSql, ' AND ');

        // 执行SELECT语句
        $selectStmt = $pdo->prepare($selectSql);
        $selectStmt->execute($selectParams);

        // 判断条件是否匹配
        if ($selectStmt->rowCount() > 0) {
            // 条件匹配，执行UPDATE语句
            $updateSql = 'UPDATE ' . $table . ' SET ';
            $updateParams = array();
            foreach ($data as $key => $value) {
                $updateSql .= $key . ' = :' . $key . ', ';
                $updateParams[':' . $key] = $value;
            }
            $updateSql = rtrim($updateSql, ', ');
            $updateSql .= ' WHERE ';
            foreach ($condition as $key => $value) {
                $updateSql .= $key . ' = :' . $key . ' AND ';
                $updateParams[':' . $key] = $value;
            }
            $updateSql = rtrim($updateSql, ' AND ');

            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute($updateParams);
        } else {
            // 条件不匹配，执行INSERT语句
            $insertSql = 'INSERT INTO ' . $table . ' (';
            $insertValues = '';
            $insertParams = array();
            foreach ($data as $key => $value) {
                $insertSql .= $key . ', ';
                $insertValues .= ':' . $key . ', ';
                $insertParams[':' . $key] = $value;
            }
            $insertSql = rtrim($insertSql, ', ');
            $insertValues = rtrim($insertValues, ', ');
            $insertSql .= ') VALUES (' . $insertValues . ')';

            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->execute($insertParams);
        }

        $pdo->commit();
    } catch (PDOException $e) {
        // 错误处理，可以记录日志或回滚事务
        //$pdo->rollBack();
        //error_log('Database error: ' . $e->getMessage());
    }
}
function tokentime($token) {
    global $database;
    // 检查令牌是否存在于数据库中
    $stmt = $database->query("SELECT logtime, username FROM users WHERE token = '$token'");
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
        $stmt = $database->query("SELECT username FROM users WHERE apikey = '$token'");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return true;
        } else {
            return false;
        }
    }
}
function apineedupdate() {
    global $database;
    // 查询是否存在api表并且获取最后更新时间
    $stmt = $database->query("SELECT time FROM api");
    if ($stmt->rowCount() > 0) {
        $lastUpdateTime = $stmt->fetchColumn();
        
        // 检查最后更新时间是否在30分钟内
        if (time() - $lastUpdateTime < 60 * 30) {
            return false;
        }
    }
    
    // 如果没有api表或者最后更新时间超过了30分钟，则需要更新
    return true;
}