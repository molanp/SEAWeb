<?php
include_once($_SERVER['DOCUMENT_ROOT']."/services/connect.php");
include_once($_SERVER['DOCUMENT_ROOT']."/services/until.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (tokentime($_POST)) {
        include_once($_SERVER['DOCUMENT_ROOT']."/services/update.php");
        $dbData = [];
        $stmt = $DATABASE->query("SELECT item FROM setting");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dbData[] = $row['item'];
        }
        $itemsToAdd = array_diff(array_keys(UP_SYS), $dbData);
        $itmsToDelete = array_diff($dbData, array_keys(UP_SYS));

        if (!empty($itemsToAdd)) {
            $sqlAdd = "INSERT INTO setting (item, value, info) VALUES (:item, :value, :info)";
            $stmtAdd = $DATABASE->prepare($sqlAdd);
            foreach ($itemsToAdd as $item) {
                $stmtAdd->bindParam(':item', $item);
                $stmtAdd->bindValue(':value', UP_SYS[$item]['value']);
                $stmtAdd->bindValue(':info', UP_SYS[$item]['info']);
                $stmtAdd->execute();
            }
        }

        if (!empty($itemsToDelete)) {
            $sqlDelete = "DELETE FROM setting WHERE item = :item";
            $stmtDelete = $DATABASE->prepare($sqlDelete);
            foreach ($itemsToDelete as $item) {
                $stmtDelete->bindParam(':item', $item);
                $stmtDelete->execute();
            }
        }
        (new logger())->info("用户执行更新设置项操作");
        _return_("更新成功");
    } else {
        _return_("莫的权限", 403);
    }
} else {
    _return_('Bad Request',400);
}