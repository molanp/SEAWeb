<?php
class Config
{
    private $className;
    private $filePath;
    private $config;

    public function __construct()
    {
        $this->className = get_called_class();

        $this->filePath = $_SERVER["DOCUMENT_ROOT"] . "/configs/main_config.json";

        if (!file_exists($this->filePath)) {
            $defaultValue = [];
            file_put_contents($this->filePath, json_encode($defaultValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        $mainConfig = json_decode(file_get_contents($this->filePath), true);

        $this->config = $mainConfig;
    }

    public function get($key, $default = "")
    {
        if (!array_key_exists($this->className, $this->config) || !array_key_exists($key, $this->config[$this->className])) {
            return $default;
        }

        return $this->config[$this->className][$key][0];
    }

    public function set($key, $value, $help="")
    {
        $this->config[$this->className][$key] = [$value, $help];

        file_put_contents($this->filePath, json_encode($this->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function delete($key, $default = "")
    {
        if (!array_key_exists($this->className, $this->config) || !array_key_exists($key, $this->config[$this->className])) {
            return $default;
        }

        unset($this->config[$this->className][$key]);

        file_put_contents($this->filePath, json_encode($this->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    
    public function getAll() {
        return $this->config;
    }
}