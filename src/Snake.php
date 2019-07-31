<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/7/31
 * Time: 13:14
 */

namespace Snake;

use Snake\Component\Config;

class Snake {
    use Config;

    private static $_server = null;

    public static function run() {
        if(self::$_server === null){
            list($swoole, $server) = self::startServer(self::$_config['server']);
            
        }
    }

    public static function startServer($conf){
        switch ($conf['server_type']) {
            case 1:
                new \Swoole\Http\Server();
        }
    }


}