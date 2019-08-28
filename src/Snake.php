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

    const SWOOLE_SERVER = 0;
    const SWOOLE_HTTP_SERVER = 1;
    const SWOOLE_WEBSOCKET_SERVER = 2;


    private static $_server = null;

    public static function run() {
        if (self::$_server === null) {
            list($swoole, $server) = self::startServer(self::$_config['server']);
            @swoole_set_process_name('snake_master_' . sha1(serialize(self::$_config)));
            $server->start();
        }
    }

    public static function startServer($conf) {
        $server = null;
        switch ($conf['server_type']) {
            case self::SWOOLE_SERVER:
                $server = new \Swoole\Server($conf['ip'], $conf['port']);
                break;
            case self::SWOOLE_HTTP_SERVER:
                $server = new \Swoole\Http\Server($conf['ip'], $conf['port'], $conf['mode'], $conf['socket_type']);
                break;
            case self::SWOOLE_WEBSOCKET_SERVER:
                $server = new \Swoole\WebSocket\Server($conf['ip'], $conf['port']);
            default:
                echo '未知服务' . PHP_EOL;
                exit();
        }
        if (isset($conf['set'])) {
            $server->set($conf['set']);
        }
        $call = ['workerstart' => 'onWorkerStart', 'managerstart' => 'onManagerStart'];
        $obj = self::onEvent($server, $conf['event'], $conf, $call);

        return [$server, $obj];
    }

    public static function onEvent($server, $class, $conf, $call = []) {
        $rfl     = new \ReflectionClass($class);
        $methods = $rfl->getMethods(\ReflectionMethod::IS_PUBLIC);
        $obj     = new $class($server, $conf);
        foreach ($methods as $function) {
            if (strpos($function->class, 'Snake\\Swoole\\') === false) {
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