<?php

/**
 * 社区PRC数据中间层client使用类
 *
 * @Author: piniing@qq.com
 * @Date:   2017-06-19 15:58:47
 * @Last Modified by:   philips
 * @Last Modified time: 2017-10-11 09:19:45
 */

// namespace Wzly\Forum;

interface RpcProvider {
    // 用于UV统计
    public static function getUdid();
    public static function getUserOpenId();
}

abstract class Lib_Forum_Rpc implements RpcProvider {
    public static $appid = 0;
    public static $secret = '';
    public static $apiurl = '';
    public static $timeout = 5000; //超时时间

    /**
     * 获取设备识别码，用于UV统计
     *
     * @return string
     */
    abstract public static function getUdid();

    /**
     * 获取用户id
     *
     * @return string
     */
    abstract public static function getUserOpenId();

    /**
     * 生成签名
     *
     * @param  array    $params 不提交从func_get_args获取
     * @return array
     */
    protected static function signature(array $params) {
        $appid = static::$appid;
        $secret = static::$secret;
        $timestamp = time();
        $udid = static::getUdid();
        $userOpenId = static::getUserOpenId();

        $params['appid'] = $appid;
        $params['timestamp'] = $timestamp;
        $params['ticket'] = sha1($secret . $timestamp);

        $params['udid'] = $udid;
        $params['user_open_id'] = $userOpenId;

        ksort($params);

        $string1 = strtolower(http_build_query($params));

        $signature = sha1($string1);

        $params = [
            'appid' => $appid,
            'timestamp' => $timestamp,
            'udid' => $udid,
            'user_open_id' => $userOpenId,
            'signature' => $signature,
        ];

        return $params;
    }

    /**
     * 动态执行对应API
     *
     * @param  string $method controller_action。示例：$client->channel_fetch(1,2)
     * @param  array $args   调用参数
     * @return array
     */
    public static function __callStatic($method, $args) {
        try {

            if (strpos($method, '_') === false) {
                echo "请求方式错误：method：controller_action";
                // throw new \Exception("请求方式错误：method：controller_action", 1);
            }

            list($controller, $action) = explode('_', $method);

            $client = new \Yar_client(static::$apiurl . $controller);
            $client->SetOpt(YAR_OPT_CONNECT_TIMEOUT, static::$timeout);
            array_unshift($args, self::signature($args));
            return call_user_func_array([$client, $action], $args);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
