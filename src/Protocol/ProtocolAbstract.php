<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/8/7
 * Time: 10:00
 */

namespace Snake\Protocol;

abstract class ProtocolAbstract {

    abstract public static function length($string);
    abstract public static function encode($str);
    abstract public static function decode($str);

}