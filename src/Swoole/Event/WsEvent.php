<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/8/13
 * Time: 17:44
 */

namespace Snake\Swoole\Event;

use Snake\Exceptions\RouteException;
use Snake\Http\Router;
use Snake\Swoole\Server;

trait WsEvent {

    public function onMessage(\Swoole\WebSocket\Server $server, \Swoole\WebSocket\Frame $frame) {

    }

    /**
     * @todo 未完成
     *
     * @param \Swoole\Http\Request  $request
     * @param \Swoole\Http\Response $response
     * Author: DQ
     */
    public function onHandShake(\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
        $secWebSocketKey = $request->header['sec-websocket-key'];
        $patten          = '#^[+/0-9A-Za-z]{21}[AQgw]==$#';
        if (0 === preg_match($patten, $secWebSocketKey) || 16 !== strlen(base64_decode($secWebSocketKey))) {
            $response->end();

            return false;
        }

        $key = base64_encode(sha1($request->header['sec-websocket-key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));

        $headers = [
            'Upgrade'               => 'websocket',
            'Connection'            => 'Upgrade',
            'Sec-WebSocket-Accept'  => $key,
            'Sec-WebSocket-Version' => '13',
        ];
        if (isset($request->header['sec-websocket-protocol'])) {
            $headers['Sec-WebSocket-Protocol'] = $request->header['sec-websocket-protocol'];
        }

        foreach ($headers as $key => $header) {
            $response->header($key, $header);
        }

        $response->status(101);
        $response->end();

        return true;
    }

    public function onOpen(\Swoole\WebSocket\Server $server, \Swoole\Http\Request $request) {
        return true;
    }

    protected function wsRouter(\Swoole\WebSocket\Server $server, \Swoole\WebSocket\Frame $frame) {
        $info = json_decode($frame->data, true);
        if (!$info || isset($info['u']) || !isset($info['d'])) {
            $this->push($frame->fd, '格式错误');

            return false;
        }

        $frame->data = $info['d'];
        $frame->uuid = uniqid();
        try {
            $router  = new Router();
            $server  = $this instanceof Server ? $this : $this->server;
            $session = isset($this->session[ $frame->fd ]) ? $this->session[ $frame->fd ] : null;
            list($frame->class, $frame->method, $mids, $action, $frame->args) = $router->explain('ws', $info['u'], $frame, $server, $session);
            $f    = $router->getExecAction($mids, $action, $frame, $server, $session);
            $data = $f();
        } catch (RouteException $e) {
            $data = $e->getMessage();
        } catch (\Throwable $e) {
            $data = $e->getMessage();
        }
        if ($data){
            $server->push($frame->fd, $data);
        }
    }

}