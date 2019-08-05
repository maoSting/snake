<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/8/2
 * Time: 16:23
 */

namespace Snake\Swoole\Event;

trait HttpEvent {

    public function onRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {

    }

    protected function httpRouter(\Swoole\Http\Request $request, \Swoole\Http\Response $response){
        



    }
}