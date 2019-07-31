<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/7/31
 * Time: 13:21
 */

namespace Snake\Component;

trait Singleton {
    protected static $_instance;
    public static function getInstance(...$args){
        if (!isset(self::$_instance)){
            self::$_instance = new static(...$args);
        }
        return self::$_instance;
    }

}