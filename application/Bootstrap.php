<?php

class Bootstrap extends Yaf_Bootstrap_Abstract {
    public function _initDbConf() {
        if (!Db_Manager::getConfig()) {
            $appConfig = Yaf_Application::app()->getConfig();
            $dbConfig = $appConfig->database->config->toArray();
            $config = array(
                'database_type' => 'mysql',
                'server' => $dbConfig['host'],
                'username' => $dbConfig['username'],
                'password' => $dbConfig['password'],
                'database_name' => $dbConfig['dbname'],
                'port' => isset($dbConfig['port']) ? $dbConfig['port'] : 3306,
                'option' => isset($dbConfig['option']) ? $dbConfig['option'] : array(),
            );
            Db_Manager::setConfig($config);
        }
    }
}