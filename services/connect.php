<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/configs/config.php");
include_once("watchdog.php");
include_once("logger.php");

if ($sqlite_mode === true) {
    define('DATABASE', new PDO("sqlite:".$_SERVER["DOCUMENT_ROOT"]."/data/main.db"));
} elseif ($sqlite_mode === false) {
    define('DATABASE', new PDO($bind, $mysql_username, $mysql_password));
} else {
    throw new Exception("数据库配置未填写，请前往configs/config.php填写！");
};
DATABASE->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function UpdateOrCreate($pdo, $table, $data = []) {
    global $sqlite_mode;
    try {
        $pdo->beginTransaction();
        if($sqlite_mode===true) {
            $insertSql = 'INSERT OR REPLACE INTO ' . $table . ' (';
            $insertValues = '';
            $insertParams = [];
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
        } else {
            $insertSql = 'INSERT INTO ' . $table . ' (';
            $insertValues = '';
            $insertParams = [];
            foreach ($data as $key => $value) {
                $insertSql .= $key . ', ';
                $insertValues .= ':' . $key . ', ';
                $insertParams[':' . $key] = $value;
            }
            $insertSql = rtrim($insertSql, ', ');
            $insertValues = rtrim($insertValues, ', ');
            $insertSql .= ') VALUES (' . $insertValues . ') ON DUPLICATE KEY UPDATE ';

            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->execute($insertParams);
        }
        $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        (new logger())->error('Database error: ' . $e->getMessage());
    }
}

function tokentime($token=123456) {
    $token = $token ?: 123456;
    // 检查令牌是否存在于数据库中
    $stmt = DATABASE->query("SELECT logtime, username FROM users WHERE token = '$token'");
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
        $stmt = DATABASE->query("SELECT username FROM users WHERE apikey = '$token'");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return true;
        } else {
            return false;
        }
    }
}

function apineedupdate() {
    // 查询是否存在api表并且获取最后更新时间
    $stmt = DATABASE->query("SELECT MAX(time) FROM api");
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
