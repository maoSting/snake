<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/8/5
 * Time: 14:45
 */

namespace Snake\Swoole;

class Request extends \Snake\Http\Request {

    private $_httpRequest;
    private $_requestId;


    public function __construct(\Swoole\Http\Request $request) {
        $this->_httpRequest = $request;
        $this->files = $request->files;

        $this->fd = $request->fd;
        $this->get = &$request->get;
        $this->post = &$request->post;
        $this->cookie = &$request->cookie;
        $this->server = &$request->server;
        $this->_requestId = uniqid();
    }

    public function requestId() {
        return $this->_requestId;
    }

    public function input(){
        return $this->_httpRequest->rawContent();
    }

    public function params($key = '', $default = ''){
        return array_get_value_by_key($this->get + $this->post, $key, $default);
    }

}