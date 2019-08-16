<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/8/16
 * Time: 15:01
 */
if (defined('APP_PATH')) {
    exit('undefined APP_PATH exist');
}
if (!defined('DEBUG')) {
    define('DEBUG', false);
}
define('SNAKE_VERSION', '0.0.1');

require __DIR__ . '/functions.php';