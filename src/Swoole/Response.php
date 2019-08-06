<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/8/6
 * Time: 16:47
 */

namespace Snake\Swoole;

use \Swoole\Http\Request;

class Response extends \Snake\Http\Response {

    /**
     * @var \Swoole\Http\Response
     */
    private $httpResponse;

    /**
     * @var \Swoole\Http\Request
     */
    protected $httpRequest;

    public function __construct(Request $request, \Swoole\Http\Response $response) {
        $this->httpRequest = $request;
        $this->httpResponse = $response;
    }


    public function header($key = '', $val = '', $replace = true, $code = null) {
        $this->httpResponse->header($key, $val);
        if ($code) {
            $this->code($code);
        }
    }

    public function code($code) {
        $this->httpResponse->status($code);
    }

    public function cookie(...$args) {
        $this->httpResponse->cookie(...$args);
    }

    public function write($html) {
        $this->httpResponse->write($html);
    }

    public function __call($name, $arguments) {
        if (method_exists($this->httpResponse, $name)){
            return $this->httpResponse->$name(...$arguments);
        }
    }



}