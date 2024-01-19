<?php
include_once("connect.php");
class Data
{
    /**

     * 获取指定belong下的值

     * @param $belong 要获取的项名

     * @return data

     */

    public function get($belong = '', $time = false)
    {
        global $DATABASE;

        $stmt = $DATABASE->prepare("SELECT item, content, time FROM data WHERE belong = :belong");
        $stmt->bindParam(':belong', $belong);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            $text = [];
            foreach ($result as $row) {
                if ($time === true) {
                    $text[$row["item"]] = [$row["content"], $row["time"]];
                } else {
                    $text[$row["item"]] = $row["content"];
                }
            }
            return $text;
        }
        return [];
    }

    public function set($belong, $data)
    {
        global $DATABASE;

        foreach ($data as $item => $value) {
            $stmt = $DATABASE->prepare("REPLACE INTO data (item, content, time, belong) VALUES (:item, :value, :time, :belong)");
            $stmt->bindParam(':item', $item);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':belong', $belong);
            $stmt->bindValue(':time', date('Y-m-d H:i:s'));
            $stmt->execute();
        }
    }

    public function delete($belong, $items = [])
    {
        global $DATABASE;

        if (isset($items)) {
            foreach ($items as $item) {
                $stmt = $DATABASE->prepare("DELETE FROM data WHERE item = :item AND belong = :belong");
                $stmt->bindParam(':item', $item);
                $stmt->bindParam(':belong', $belong);
                $stmt->execute();
            }
        } else {
            $stmt = $DATABASE->prepare("DELETE FROM data WHERE belong = :belong");
            $stmt->bindParam(':belong', $belong);
            $stmt->execute();
        }
    }
}
