<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/8/14
 * Time: 14:14
 */

namespace Snake\Swoole\Server;

use Snake\Swoole\Event\WsEvent;
use Snake\Swoole\Server;

class WsServer extends Server {

    use WsEvent;

    protected $session = [];

}