<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/8/5
 * Time: 17:55
 */

namespace Snake\Cache;

abstract class Cache {
    abstract public function get($key);
    abstract public function set($key, $value, $ttl);
    abstract public function del($key);
    abstract public function flush();
}