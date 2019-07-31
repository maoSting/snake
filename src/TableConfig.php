<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/7/31
 * Time: 15:56
 */

namespace Snake;

use Snake\Component\Singleton;
use Swoole\Table;

class TableConfig {

    use Singleton;

    private $_table;

    public function __construct() {
        $this->_table = new Table();
        $this->_table->column('data', Table::TYPE_STRING, 2048);
        $this->_table->create();
    }


    public function setConfig($key = '', $value){
        $data['data'] = serialize($value);
        $this->_table->set($key, $data);
    }

    public function getConfig($keyPath = ''){
        if($keyPath == ''){

        }
    }

}