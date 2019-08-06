<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/8/6
 * Time: 17:10
 */

namespace Snake\Swoole;

class Server {


    protected $conf = [];

    protected $protocol = null;

    protected $server = null;

    public $worker_id = 0;
    public $is_task = false;
    public $pid = 0;

    public function __construct(\Swoole\Server $server, array $conf) {
        $this->server = $server;
        $this->conf = $conf;
        if (isset($conf['pack_protocol'])){
            $this->protocol = $conf['pack_protocol'];
        }
    }

    public function send
}