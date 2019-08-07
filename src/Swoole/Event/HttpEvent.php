<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/8/2
 * Time: 16:23
 */

namespace Snake\Swoole\Event;

use Snake\Http\Router;
use Snake\Swoole\Server;

trait HttpEvent {

    public function onRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {

    }

    protected function httpRouter(\Swoole\Http\Request $request, \Swoole\Http\Response $response){
        $req = new \Snake\Swoole\Request($request);
        $requestId = $req->requestId();
        $res = new \Snake\Swoole\Response($request, $response);
        try{
            $route = new Router();
            $server = $this instanceof Server ? $this : $this->server;

            $req->method();
            $req->uri();





        }catch (\HttpException $e){

        }catch (\Throwable $exception){

        }


    }
}