<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/8/13
 * Time: 17:14
 */

namespace Snake\Http;

class Controller {

    /**
     * @var \Snake\Http\Request
     */
    protected $request = null;

    /**
     * @var \Snake\Http\Response
     */
    protected $response = null;

    /**
     * @var \Snake\Swoole\Server\HttpServer
     */
    protected $server;

    protected $go_id = -1;

    public function __construct($request, $response, $server = null) {
        $this->server   = $server;
        $this->request  = $request;
        $this->response = $response;
    }

    public function __destruct() {
        // TODO: Implement __destruct() method.
    }

    protected function session(){
        return $this->response->session();
    }

    protected function json($data){
        $this->response->header('Content-type', 'application/json');
        return json_encode(['data'=>$data, 'code'=>0, 'r_id'=>0]);
    }

}