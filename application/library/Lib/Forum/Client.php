<?php

class Lib_Forum_Client extends Lib_Forum_Rpc {
    public static $appid = 2;
    public static $apiurl = 'http://rpc.forum.meilitashuo.com/index/';
    public static $secret = '291d40ba46c62ca2';

    /**
     * 获取客户端imei
     *
     * @return string
     */
    public static function getUdid() {
        return 'uuid2';
    }

    /**
     * 获取用户open_id
     *
     * @return string
     */
    public static function getUserOpenId() {
        return '';
    }
}