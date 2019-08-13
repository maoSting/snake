<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/8/13
 * Time: 15:04
 */

namespace Snake\Swoole\Listener;

class Port {

    protected $conf = [];

    protected $server;

    protected $protocol = null;

    public function __construct($server, $conf) {
        $this->server = $server;
        $this->conf   = $conf;
        if (isset($conf['pack_protocol'])) {
            $this->protocol = $conf['pack_protocol'];
        }
    }

    public function send($fd, $data, $from_id = 0) {
        if ($this->protocol) {
            $data = $this->protocol::encode($data);
        }
        $this->server->send($fd, $data, $from_id, false);
    }

    public function onClose(\Swoole\Server $server, $fd, $reactor_id) {

    }

    public function __call($name, $arguments) {
        return $this->server->$name(...$arguments);
    }
}