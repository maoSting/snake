<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/8/13
 * Time: 17:44
 */

namespace Snake\Swoole\Event;

trait WsEvent {

    public function onMessage(\Swoole\WebSocket\Server $server, \Swoole\WebSocket\Frame $frame) {

    }

    /**
     * @todo 未完成
     * @param \Swoole\Http\Request  $request
     * @param \Swoole\Http\Response $response
     * Author: DQ
     */
    public function onHandShake(\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
        $secWebSocketKey = $request->header['sec-websocket-key'];

    }
}