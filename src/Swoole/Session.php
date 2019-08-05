<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/8/5
 * Time: 17:28
 */

namespace Snake\Swoole;

use Snake\Cache\File;
use Snake\Http\Response;

class Session {
    private $data = [];

    private $name = '';

    private $session_id = '';

    private $time = 0;

    /**
     * @var \Snake\Swoole\File
     */
    private $drive;

    private $prefix = 'session_';

    public function __construct(Response $response = null, $id = null) {
        $this->name = config_get('session.name');
        if ($id) {
            $this->session_id = $id;
        } else if($response){
            $this->session_id = $response->getHttpRequest()->cookie($this->name);
            if(!$this->session_id){
                $this->session_id = uniqid();
            }
        }

        if (!$this->session_id){
            exit('无法获取session id');
            return;
        }

        $this->time = intval(ini_get('session.gc_maxlifetime'));

        if (config_get('session.drive') == 'file') {
            // @todo 添加文件缓存
            $this->drive = new File();
        }else{
            exit('驱动类型错误 id');
        }

        if($response){
            $response->cookie($this->name, $this->session_id, $this->time+ time(), '/');
        }
        $this->data = unserialize($this->drive->get($this->name.$this->session_id))
    }

    public function getSessionId(){
        return $this->session_id;
    }



}