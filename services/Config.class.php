<?php
include_once("connect.php");

class Data
{
    /**

     * 获取指定belong下的值

     * @param $belong 要获取的项名

     * @param $default 默认值

     * @return data

     */

    public function get($belong = '', $default = '')
    {
        global $DATABASE;

        $stmt = $DATABASE->prepare("SELECT item, content FROM data WHERE belong = :belong");
        $stmt->bindParam(':belong', $belong);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            $text = [];
            foreach ($result as $row) {
                $text[$row["item"]] = $row["content"];
            }
            return $text;
        } else {
            return $default;
        }
    }

    public function time($belong = '', $default = '')
    {
        global $DATABASE;

        $stmt = $DATABASE->prepare("SELECT item, time FROM data WHERE belong = :belong");
        $stmt->bindParam(':belong', $belong);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            $text = [];
            foreach ($result as $row) {
                $text[$row["item"]] = $row["time"];
            }
            return $text;
        } else {
            return $default;
        }
    }

    public function set($belong, $data)
    {
        global $DATABASE;

        foreach ($data as $item => $value) {
            $stmt = $DATABASE->prepare("REPLACE INTO data (item, content, time, belong) VALUES (:item, :value, :time, :belong)");
            $stmt->bindParam(':item', $item);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':belong', $belong);
            $stmt->bindParam(':time', date('Y-m-d H:i:s'));
            $stmt->execute();
        }
    }
}
