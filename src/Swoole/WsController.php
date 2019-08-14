<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/8/14
 * Time: 14:12
 */

namespace Snake\Swoole;

class WsController {
    protected $frame;

    protected $server;

    protected $session;

    protected $go_id;

    public function __construct($frame, $server, $session = null) {
        $this->frame = $frame;
        $this->server = $server;
        $this->session = $session;
    }
}