<?php
ini_set("date.timezone","Asia/Shanghai");
/**
 * Class logger
 * 一个用于日志记录的类，支持 info、warn、debug 和 error 四种级别的日志记录。
 */
class logger {
    /**
     * 记录 info 级别的日志。
     *
     * @param string $str 要记录的日志内容
     * @return void
     */
    public function info($str) {
        $time = date("Y-m-d H:i:s");
        $level = "INFO";
        $content = $str;
        $this->writeToDatabase($time, $level, $content);
    }

    /**
     * 记录 warn 级别的日志。
     *
     * @param string $str 要记录的日志内容
     * @return void
     */
    public function warn($str) {
        $time = date("Y-m-d H:i:s");
        $level = "WARN";
        $content = $str;
        $this->writeToDatabase($time, $level, $content);
    }

    /**
     * 记录 debug 级别的日志。
     *
     * @param string $str 要记录的日志内容
     * @return void
     */
    public function debug($str) {
        $time = date("Y-m-d H:i:s");
        $level = "DEBUG";
        $content = $str;
        $this->writeToDatabase($time, $level, $content);
    }

    /**
     * 记录 error 级别的日志。
     *
     * @param string $str 要记录的日志内容
     * @return void
     */
    public function error($str) {
        $time = date("Y-m-d H:i:s");
        $level = "ERROR";
        $content = $str;
        $this->writeToDatabase($time, $level, $content);
    }

    /**
     * 将日志写入数据库。
     *
     * @param string $time 日志记录时间
     * @param string $level 日志级别
     * @param string $content 日志内容
     * @return void
     */
    private function writeToDatabase($time, $level, $content) {
        global $DATABASE;
        $stmt = $DATABASE->prepare("INSERT INTO log (time, level, content) VALUES (?, ?, ?)");
        $stmt->execute([$time, $level, $content]);
    }
}