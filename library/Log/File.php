<?php
class Log_File {
    private $_basePath = APP_PATH.'/logs/';
    private $_path = '';
    private static $_instance = null;

    public function __construct() {
        $appConfig = Yaf_Application::app()->getConfig();
        $logConfig = $appConfig->logs->config->toArray();
        $basePath = isset($logConfig['path']) ? $logConfig['path'] : '';
        $this->_path = ($basePath ? $basePath : $this->_basePath) . "/api/";
        if(!is_dir($this->_path)) {
            mkdir($this->_path);
        }
    }

    public static function _instance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function write($msg) {
        error_log(date("[Y-m-d H:i:s] ") . $msg . "\n", 3, $this->_path . "api-log-" . date("Ymd"));
    }

}