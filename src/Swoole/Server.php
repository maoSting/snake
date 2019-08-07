<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/8/6
 * Time: 17:10
 */

namespace Snake\Swoole;

use Swoole\Process;

class Server {

    protected $conf = [];

    protected $protocol = null;

    protected $server = null;

    public $worker_id = 0;
    public $is_task   = false;
    public $pid       = 0;

    public function __construct(\Swoole\Server $server, array $conf) {
        $this->server = $server;
        $this->conf   = $conf;
        if (isset($conf['pack_protocol'])) {
            $this->protocol = $conf['pack_protocol'];
        }
    }

    public function send($fd, $data, $serverSocket, $protocol = true) {
        if ($protocol) {
            $data = $this->protocol::encode($data);
        }
        $this->server->send($fd, $data, $serverSocket);
    }

    public function onStart(\Swoole\Server $server) {

    }

    public function onShutdown(\Swoole\Server $server) {

    }

    public function onWorkStart(\Swoole\Server $server, $worker_id) {
        $this->is_task   = $server->taskworker;
        $this->worker_id = $worker_id;
        $this->pid       = $server->worker_pid;

        @swoole_set_process_name(($this->is_task ? 'snake_task_' : 'snake_work_') . $worker_id);
        Process::signal(SIGPIPE, function ($signo) {
            echo "socket close" . PHP_EOL;
        });
    }

    public function onWorkerStop(\Swoole\Server $server, $worker_id) {

    }

    public function onWorkerExit(\Swoole\Server $server, $worker_id) {

    }

    public function onWorkerError(\Swoole\Server $server, $worker_id, $worker_pid, $exit_code, $signal) {

    }

    public function onClose(\Swoole\Server $server, $fd, $reset = false){

    }

    public function onPipeMessage(\Swoole\Server $server, $message, $dst_worker_id){

    }

    public function onManagerStart(\Swoole\Server $server){
        @swoole_set_process_name("snake_manager");
    }

    public function onManagerStop(\Swoole\Server $server){

    }

    public function __call($name, $arguments) {
        if (method_exists($this->server, $name)){
            $this->server->$name(...$arguments);
        }else{
            throw new \Exception('method not exist', 1);
        }
    }

}