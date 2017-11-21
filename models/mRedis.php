<?php

/**
 * redis单例模型
 */
class mRedis {

    private static $_redis;

    public function __construct() {

    }

    /**
     * redis 单例模式
     * @return Redis Sigle Instance
     */
    public static function get_instance() {
        global $config;
        if ($config->redis_on && extension_loaded('redis')) {
            if (!(self::$_redis instanceof self)) {
                try {
                    self::$_redis = new Redis();
                    self::$_redis->connect($config->redis_host, $config->redis_port);
                    if ($config->redis_auth != '') {
                        self::$_redis->auth($config->redis_auth);
                    }
                } catch (Exception $ex) {
                    Util::log($ex->getMessage());
                    return false;
                }
            }
            return self::$_redis;
        } else {
            return false;
        }
    }

    /**
     * @param $key
     * @return string
     */
    public static function getKey($key) {
        return APPID . ':' . $key;
    }

}
