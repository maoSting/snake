<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/7/30
 * Time: 17:16
 */

namespace Snake\Swoole\Server;


use Snake\Swoole\Event\HttpEvent;
use Snake\Swoole\Server;

class HttpServer extends Server {

    use HttpEvent;
}