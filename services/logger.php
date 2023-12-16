<?php
ini_set("date.timezone","Asia/Shanghai");
class logger {

    public function info($str) {
        $time = date("Y-m-d H:i:s");
        $level = "INFO";
        $content = $str;
        $this->writeToDatabase($time, $level, $content);
    }

    public function warn($str) {
        $time = date("Y-m-d H:i:s");
        $level = "WARN";
        $content = $str;
        $this->writeToDatabase($time, $level, $content);
    }

    public function debug($str) {
        $time = date("Y-m-d H:i:s");
        $level = "DEBUG";
        $content = $str;
        $this->writeToDatabase($time, $level, $content);
    }

    public function error($str) {
        $time = date("Y-m-d H:i:s");
        $level = "ERROR";
        $content = $str;
        $this->writeToDatabase($time, $level, $content);
    }

    private function writeToDatabase($time, $level, $content) {
        global $DATABASE;
        $stmt = $DATABASE->prepare("INSERT INTO log (time, level, content) VALUES (?, ?, ?)");
        $stmt->execute([$time, $level, $content]);
    }
}
