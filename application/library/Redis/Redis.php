<?php

class KV_Redis {

    const timeOut = 3;
    public static $instance = NULL;
    protected static $_redisConfig = [];

    private static function createInstance() {
        self::$_redisConfig = Yaf_Registry::get("config")->redis->site->remote->toArray();
        if (self::$_redisConfig['mode'] == 'single') {
            try {
                self::$instance = new \Redis();
                self::$instance->connect(self::$_redisConfig['host'], self::$_redisConfig['port'], self::timeOut);
                if (self::$_redisConfig['password'] != "") {
                    self::$instance->auth(self::$_redisConfig['password']);
                }
            } catch (RedisException $e) {
                echo 'Caught Redis exception: ', $e->getMessage(), "\n";
            }
        } elseif (self::$_redisConfig['mode'] == 'distributed') {
            try {
                if (is_array(self::$_redisConfig) && count(self::$_redisConfig)) {
                    $distributed = [];
                    foreach (self::$_redisConfig as $config) {
                        $distributed[] = $config['host'] . ':' . $config['port'];
                    }
                }
                self::$instance = new RedisArray($distributed);
            } catch (RedisException $e) {
                echo 'Caught Redis exception: ', $e->getMessage(), "\n";
            }
        } else {
            throw new Exception("Redis Config Error", 1);
        }
        return self::$instance;
    }

    private function __clone() {}

    public static function getInstance() {
        if (!isset(self::$instance) || is_null(self::$instance)) {
            self::$instance = self::createInstance();
        }
        return self::$instance;
    }
}