<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/8/2
 * Time: 16:24
 */

namespace Snake\Http;

class Request {
    protected $server  = [];
    protected $cookie  = [];
    protected $get     = [];
    protected $post    = [];
    protected $files   = [];
    protected $request = [];

    public $fd     = 0;
    public $args   = [];
    public $class  = '';
    public $method = '';

    public function __construct() {
        $this->server  = &$_SERVER;
        $this->cookie  = &$_COOKIE;
        $this->get     = &$_GET;
        $this->post    = &$_POST;
        $this->files   = &$_FILES;
        $this->request = &$_REQUEST;
    }

    public function cookie($key = null, $default = null){
        return array_get_value_by_key($this->cookie, $key, $default);
    }

    public function ip() {
        return array_get_value_by_keys($this->server, ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR']);
    }

    public function server($key = '', $default = null) {
        return array_get_value_by_key($this->server, $key, $default);
    }

    public function get($key = '', $default = null) {
        return array_get_value_by_key($this->get, $key, $default);
    }

    public function post($key = '', $default = null) {
        return array_get_value_by_key($this->post, $key, $default);
    }

    public function files() {
        return $this->files;
    }

    public function isJson() {
        if($this->server('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest' || strpos($this->server('HTTP_ACCEPT'), '/json') !== false){
            return true;
        }else{
            return false;
        }
    }

}