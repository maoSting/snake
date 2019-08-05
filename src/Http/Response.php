<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/8/5
 * Time: 14:43
 */

namespace Snake\Http;

use Snake\Exceptions\HttpException;

class Response {

    protected $httpRequest;
    protected $_session = null;

    private $_http_status = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',            // RFC2518
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',          // RFC4918
        208 => 'Already Reported',      // RFC5842
        226 => 'IM Used',               // RFC3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',    // RFC7238
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',                                               // RFC2324
        421 => 'Misdirected Request',                                         // RFC7540
        422 => 'Unprocessable Entity',                                        // RFC4918
        423 => 'Locked',                                                      // RFC4918
        424 => 'Failed Dependency',                                           // RFC4918
        425 => 'Reserved for WebDAV advanced collections expired proposal',   // RFC2817
        426 => 'Upgrade Required',                                            // RFC2817
        428 => 'Precondition Required',                                       // RFC6585
        429 => 'Too Many Requests',                                           // RFC6585
        431 => 'Request Header Fields Too Large',                             // RFC6585
        451 => 'Unavailable For Legal Reasons',                               // RFC7725
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',                                     // RFC2295
        507 => 'Insufficient Storage',                                        // RFC4918
        508 => 'Loop Detected',                                               // RFC5842
        510 => 'Not Extended',                                                // RFC2774
        511 => 'Network Authentication Required',                             // RFC6585
    ];

    public function __construct(Request $request) {
        $this->httpRequest = $request;

    }

    public function getHttpRequest() {
        return $this->httpRequest;
    }

    public function header($key = '', $val = '', $replace = true, $code = null) {
        header($key . ':' . $val, $replace, $code);
    }

    public function cookie() {
        return setcookie(...func_get_args());
    }

    public function code($code) {
        if (isset($this->_http_status[ $code ])) {
            header('HTTP/1.1 ' . $code . ' ' . $this->_http_status[ $code ]);
        }
    }

    public function json($data, $callback = null) {
        $this->header('Content-type', 'application/json');
        if ($callback) {
            return sprintf('%s(%s)', $callback, $data);
        } else {
            return $data;
        }
    }

    public function write($html) {
        echo $html;
    }

    public function redirect($url, $args = []) {
        if (isset($args['time'])) {
            $this->header('Refresh', $args['time'] . ';url=' . $url);
        } else if (isset($args['httpCode'])) {
            $this->header('Location', $url, true, $args['httpCode']);
        } else {
            $this->header('Location', $url, true, 302);
        }

        return '';
    }

    public function tpl($tpl = '', $data = []) {
        if ($this->getHttpRequest()->isJson()) {
            $this->header('Content-type', 'application/json');

            return json_encode($data);
        } else {
            if (defined('APP_PATH_VIEW') === false) {
                throw new HttpException('not found view template path:APP_PATH_VIEW', 404);
            }
            $file = APP_PATH_VIEW . '/' . $tpl . '.php';
            if (!file_exists($file)) {
                throw new HttpException('not found view template path:' . $file, 404);
            }
            ob_start();
            extract($data);
            require $file;

            return ob_get_clean();
        }
    }
}