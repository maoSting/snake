<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/8/13
 * Time: 15:58
 */

namespace Snake\Swoole\Listener;

use Snake\Swoole\Event\HttpEvent;

class Http extends Port {
    use HttpEvent;
}