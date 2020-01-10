<?php
class Db_Manager {
    private static $_config = array();
    private static $_instance;

    /**
     * @params array $config
     *   array(
     *       'database_type' => 'mysql',
     *       'server' => '10.0.11.223',
     *       'username' => 'yongche',
     *       'password' => '',
     *       'database_name' => '',
     *       'port' => 3306,
     *       'charset' => 'utf8',
     *       'option' => array(
     *            PDO::ATTR_PERSISTENT => false,
     *            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
     *       ),
     *   )
     **/
    public static function setConfig(array $config) {
        self::$_config = $config;
    }

    public static function getConfig() {
        return self::$_config;
    }

    public static function init(array $options) {
        if(empty($options) || empty($options['database_type'])
            || empty($options['server']) || empty($options['port']) || empty($options['username'])
            || !isset($options['password']) || empty($options['database_name'])) {
            throw new Db_Exception("server or username or password or database_name or database_type cann't be empty");
        }
        $opts = array(
            \PDO::ATTR_PERSISTENT => false,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_STRINGIFY_FETCHES => false,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_TO_STRING,
        );
        $options['charset'] = isset($options['charset']) ? $options['charset'] : 'utf8';
        $options['option'] = isset($option['option']) ? array_merge($opts, $option['option']) : $opts;
        if(!isset(self::$_instance->_instance)) {
            self::$_instance = new Db_Mysql($options);
        }
    }

    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::init(self::$_config);
        }
        return self::$_instance;
    }
}