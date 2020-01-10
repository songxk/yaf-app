<?php

class Cache_Redis {
    private static $_instance = null;

    private function __construct() {}

    public function __clone() {}

    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::_init();
        }
        return self::$_instance;
    }

    protected static function _init() {
        $appConfig = Yaf_Application::app()->getConfig();
        $redisConfig = $appConfig->redis->config->toArray();
        $server = isset($redisConfig['server']) ? $redisConfig['server'] : '';
        $port = isset($redisConfig['port']) ? (int)$redisConfig['port'] : 6379;
        self::$_instance = new Redis();
        if (!self::$_instance->connect($server, $port)) {
            throw new Cache_Exception('can not connect to redis server');
        }
    }
}