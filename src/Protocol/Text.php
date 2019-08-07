<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/8/7
 * Time: 10:02
 */

namespace Snake\Protocol;

class Text extends ProtocolAbstract {

    // work进程管道缓存
    const MAX_LENGTH = 8 * 1024 * 1024;

    public static function length($string) {
        if(strlen($string) > self::MAX_LENGTH){
            return -1;
        }
        $length = strpos($string, PHP_EOL);
        if ($length === false){
            return 0;
        }
        return $length + 1;
    }

    public static function encode($str) {
        return trim($str).PHP_EOL;
    }

    public static function decode($str) {
        return trim($str);
    }
}