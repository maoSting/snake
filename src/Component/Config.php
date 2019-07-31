<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/7/31
 * Time: 13:24
 */

namespace Snake\Component;

trait Config {
    protected static $_config = [];
    public static function setConfig($config){
        self::$_config = $config;
    }
}