<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/7/30
 * Time: 17:18
 */
namespace Snake\Exceptions;

use Throwable;

class HttpException extends \Exception {
    public function __construct($message = "", $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}