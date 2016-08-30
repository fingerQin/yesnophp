<?php
/**
 * 基于Redis的分布式锁。
 * @author winerQin
 * @date 2016-04-26
 */
namespace winer;

use common\YCore;
class RedisMutexLock {

    /**
     * 锁的超时时间(秒)。
     * @var number
     */
    public static $timeout = 20;

    /**
     * KEY锁定之后再次尝试获取锁的间隔时间。
     * @var number
     */
    public static $sleep = 100000;

    /**
     * 当前锁的过期时间。
     * @var int
     */
    protected static $expire;

    public static function getRedis() {
        return YCore::getCache();
    }

    /**
     * 获得锁,如果锁被占用,阻塞,直到获得锁或者超时。
     * -- 1、如果$timeout参数为0，则立即返回锁。
     * -- 2、建议timeout设置为0.避免redis因为阻塞导致性能下降。
     * @param string $key 缓存KEY。
     * @param int $timeout 超时时间。如果大于0,则会反复尝试获取锁直到达到超时时间限制。
     * @return boolean 成功:true、失败:false
     */
    public static function lock($key, $timeout = null) {
        if (strlen($key) === 0) {
            YCore::exception(-1, '缓存KEY没有设置');
        }
        $start = $_SERVER['REQUEST_TIME'];
        $redis = self::getRedis();
        do {
            self::$expire = self::timeout();
            // 如果$key不存在则设置。设置成功返回TRUE。
            $acquired = $redis->setnx("Lock:{$key}", self::$expire);
            if ($acquired) {
                break;
            }
            // 如果锁已经过了锁定的时间，则恢复。
            $acquired = self::recover($key);
            if ($acquired) {
                break;
            }
            if ($timeout === 0) {
                // 如果超时时间为0，即为
                break;
            }
            usleep(self::$sleep);
        } while (!is_numeric($timeout) || time() < $start + $timeout);
        if (!$acquired) {
            return false;
        }
        return true;
    }

    /**
     * 释放锁
     * @param mixed $key 被加锁的KEY。
     * @return void
     */
    public static function release($key) {
        if (strlen($key) === 0) {
            YCore::exception(-1, '缓存KEY没有设置');
        }
        $redis = self::getRedis();
        // 只释放未过期的锁。过期了不需要释放。
        $redis->del("Lock:{$key}");
    }

    /**
     * 设置超时时间。单位(秒)。
     * @param number $timeount
     * @return void
     */
    public static function setTimeout($timeount) {
		self::$timeout = $timeount;
    }

    /**
     * 获取超时时间。
     * @return int timeout
     */
    protected static function timeout() {
        return (int) (time() + self::$timeout + 1);
    }

    /**
     * 将失效的锁恢复。
     * @param mixed $key 锁的缓存KEY。
     * @return bool
     */
    protected static function recover($key) {
        $redis = self::getRedis();
        $lockTimeout = $redis->get("Lock:{$key}");
        if ($lockTimeout > time()) { // 锁未过期，不需要恢复。
            return false;
        }
        $timeout = self::timeout();
        $currentTimeout = $redis->getset("Lock:{$key}", $timeout);
        if ($currentTimeout != $lockTimeout) {
            return false;
        }
        self::$expire = $timeout;
        return true;
    }
}