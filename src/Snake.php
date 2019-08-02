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
        if (self::$_server === null) {
            list($swoole, $server) = self::startServer(self::$_config['server']);
            @swoole_set_process_name('snake_master_' . sha1(serialize(self::$_config)))
            $server->start();
        }
    }

    public static function startServer($conf) {
        $server = null;
        switch ($conf['server_type']) {
            case 1:
                $server = new \Swoole\Http\Server($conf['ip'], $conf['port']);
                break;
            default:
                echo '未知服务' . PHP_EOL;
                exit();
        }
        if (isset($conf['set'])) {
            $server->set($conf['set']);
        }
        $obj = self::onEvent($server, $conf['event'], $conf);

        return [$server, $obj];
    }

    public static function onEvent($server, $class, $conf, $call = []) {
        $rfl     = new \ReflectionClass($class);
        $methods = $rfl->getMethods(\ReflectionMethod::IS_PUBLIC);
        $obj     = new $class($server, $conf);
        foreach ($methods as $function) {
            if (strpos($function->class, 'Snake\\Swoole\\' === false)) {
                if (substr($function->name, 0, 2) == 'on') {
                    $call[ strtolower(substr($function->name, 2)) ] = $function->name;
                }
            }
        }

        if (isset($call['receive'])) {
            $call['receive'] = '__receive';
        }
        foreach ($call as $name => $item) {
            $server->on($name, [$obj, $item]);
        }

        return $obj;
    }

}