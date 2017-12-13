<?php

abstract class BaseModel {
    private static $db=null;
    private static $redis_key = 'aiqing110';
    /*public static function db()
    {
        if (is_null(self::$db)) {
            $config = Yaf_Registry::get('config')['database'];
            self::$db = new Medoo([
                'database_type' => $config['type'],
                'database_name' => $config['dbname'],
                'server' => $config['host'],
                'username' => $config['user'],
                'password' => $config['password'],
                'charset' => 'utf8',
                'port' => 3306,
                'prefix' => $config['prefix'],
                'logging' => true,
                'command' => [
                    'SET SQL_MODE=ANSI_QUOTES'
                ]
            ]);
        }
        return self::$db;
    }*/
    public static function db($table = '') {
        self::$db = new Model();
        if ($table) {
            self::$db->setTableName($table);
        } else {
            $model = strtolower(substr(get_called_class(), 0, strlen(get_called_class())-5));
            self::$db->setTableName($model);
        }
        return self::$db;
    }

    public static function gethset($key='') {
        $val = KV_Redis::getInstance()->hGet(self::$redis_key, $key);
        return $val ? json_decode($val, true) : false;
    }

    public static function sethset($key='', $val=array()) {
        if ($key) {
            KV_Redis::getInstance()->hSet(self::$redis_key, $key, json_encode($val, JSON_UNESCAPED_UNICODE));
        }
        return $val;
    }

    public static function clearhset() {
        KV_Redis::getInstance()->hDel(self::$redis_key, 'hot_articles', 'hot_tags', 'top_banner', 'recommand_type');
    }
}