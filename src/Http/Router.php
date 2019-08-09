<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/7/30
 * Time: 17:17
 */

namespace Snake\Http;

use Snake\Cache\Cache;
use Snake\Exceptions\RouteException;

class Router {

    private        $args            = [];
    private static $info            = [];
    private static $as_info         = [];
    private static $group_info      = [];
    private static $max_group_depth = 200;

    public function getAction($method, $uri) {
        $info = $this->matchRouter($this->getKey($method, $uri));
        if (!$info) {
            throw new RouteException('Not Found', 404);
        }
        if (is_array($info)) {
            if (isset($info[0])) {
                $info = $info[0];
            } else {
                throw new RouteException('Not Found');
            }
        }
        $fm = [];

        if (is_array($info)) {
            $fm[] = $info;
            if (isset($info['middle'])) {
                foreach ($info['middle'] as $v) {
                    $fm[] = $v;
                }
            }
        } else {
            $fm[] = $info;
        }

        return $fm;
    }

    public function explain($method, $uri, ...$other_args) {
        $info = $this->getAction($method, $uri);
        $str  = is_array($info[0]) ? $info[0]['use'] : $info[0];
        list($class, $fun) = explode('@', $str);

        $funs = [];
        foreach ($info as $i => $v) {
            if ($i > 0) {
                $funs = function ($handler, ...$args) use ($v) {
                    return function () use ($v, $handler, $args) {
                        array_unshift($args, $handler);

                        return call($v, $args);
                    };
                };
            }
        }
        $action = function () use ($info, $class, $fun, $other_args) {
            $cache = 0;
            if (is_array($info[0]) && isset($info[0]['cache'])) {
                $cache = $info[0]['cache'];
                $key = md5($class.'@'.$fun.':'.implode(',', $this->args));
                $res = Cache::get($key);
                if ($res){
                    return $res;
                }
            }
            $obj = new $class(...$other_args);
            if (!method_exists($obj, $fun)){
                throw new RouteException('method not exists',2);
            }
            $res = $obj->$fun(...$this->args);
            if ($cache){
                Cache::set($key, $res, $cache);
            }
            return $res;
        };
        return [$class, $fun, $funs, $action, $this->args];
    }

    public static function group($rule, $callback) {
        $len                      = self::$max_group_depth - count(self::$group_info);
        self::$group_info[ $len ] = $rule;
        ksort(self::$group_info);
        $callback();
        unset(self::$group_info[ $len ]);
    }

    private static function withGroupAction($group_info, $action) {
        if (is_array($action)) {
            if (isset($group_info['as']) && isset($action['as'])) {
                $action['as'] = trim($group_info['as'], '.') . '.' . $action;
            }
            if (isset($group_info['namespace'])) {
                $action['use'] = '\\' . $group_info['namespace'] . '\\' . trim($action['use'], '\\');
            }
            if (isset($group_info['middle'])) {
                if (!isset($action['middle'])) {
                    $action['middle'] = [];
                }
                $action['middle'] = array_merge($action['middle'], array_reverse($group_info['middle']));
            }
            if (isset($group_info['cache'])) {
                $action['cache'] = $group_info['cache'];
            }
        } else {
            if (isset($group_info['namespace'])) {
                $action = '\\' . $group_info['namespace'] . '\\' . trim($action, '\\');
            }
            $action = ['use' => $action, 'middle' => []];
            if (isset($group_info['middle'])) {
                $action['middle'] = array_merge($action['middle'], array_reverse($group_info['middle']));
            }
            if (isset($group_info['cache'])) {
                $action['cache'] = $group_info['cache'];
            }
        }

        return $action;
    }

    private static function withGroupPath($group_info, $path) {
        $path = '/' . trim($path, '/');
        if (isset($group_info['prefix'])) {
            $prefix = trim($group_info['prefix'], '/');
            $path   = '/' . trim($prefix, '/') . $path;
        }

        return $path;
    }

    private static function createAsInfo($path, $action) {
        if (isset($action['as'])) {
            self::$as_info[ $action['as'] ] = rtrim($path, '/');
        }
    }

    private static function setPath($array, $value, $i = 0) {
        if (isset($array[ $i ])) {
            if (is_numeric($array[ $i ])) {
                $array[ $i ] = '#' . $array[ $i ];
            } else if ($array[ $i ] == '') {
                $array[ $i ] = 0;
            }

            return [$array[ $i ] => self::setPath($array, $value, $i + 1)];
        } else {
            return $value;
        }
    }

    public static function set($method, $path, $action) {
        foreach (self::$group_info as $value) {
            $action = self::withGroupAction($value, $action);
            $path   = self::withGroupPath($value, $path);
        }
        if (is_array($action)) {
            self::createAsInfo($path, $action);
        }
        $array = explode('/', $method . $path);
        if (is_array($action)) {
            $value = end($array);
            if ($value !== '') {
                $array[] = '';
            }
        }
        self::$info = array_merge_recursive(self::$info, self::setPath($array, $action));
    }

    public static function get($path, $action) {
        self::set('get', $path, $action);
    }

    public static function post($path, $action) {
        self::set('post', $path, $action);
    }

    public static function put($path, $action) {
        self::set('put', $path, $action);
    }

    public static function delete($path, $action) {
        self::set('delete', $path, $action);
    }

    public static function patch($path, $action) {
        self::set('patch', $path, $action);
    }

    public static function any($path, $action) {
        self::get($path, $action);
        self::post($path, $action);
        self::put($path, $action);
        self::delete($path, $action);
        self::patch($path, $action);
    }

    private function getKey($method, $uri) {
        $paths = explode('/', $uri);
        foreach ($paths as $i => $v) {
            if (is_numeric($v)) {
                $paths[ $i ] = '#' . $v;
            }
        }
        $path = implode('.', $paths);
        if ($path === '' || $path === '.') {
            $path = '';
        }
        $path = trim($path, '.');

        return $method . '.' . $path;
    }

    /**
     *
     * @param $key
     *            method . uri
     *
     * @return mixed
     * Author: DQ
     */
    private function matchRouter($key) {
        $array = self::$info;
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            foreach ($keys as $j => $v) {
                $array = $this->rules($array, $v);
                if ($array === null) {
                    return null;
                }
                if (is_string($array) || (count($array) == 1 && isset($array[0]))) {
                    break;
                }
            }
            $af         = array_slice($keys, $j + 1);
            $af         = array_map(function ($r) {
                return ltrim($r, '#');
            }, $af);
            $this->args = array_merge($this->args, $af);

            return $array;
        } else {
            return $this->rules($array, $key);
        }
    }

    private function rules($array, $v) {
        if (isset($array[ $v ])) {
            return $array[ $v ];
        }
        $keys = array_keys($array);
        foreach ($keys as $key) {
            $tmp = substr($key, 0, 1);
            if ($tmp == '{') {
                $_k = substr($key, 1, -1);
                if (substr($v, 0, 1) == '#') {
                    $v = substr($v, 1);
                }
                if ($_k == 'id') {
                    if (is_numeric($v)) {
                        $this->args[] = $v;

                        return $array[ $key ];
                    }
                } else {
                    $this->args[] = $v;

                    return $array[ $key ];
                }
            } else if ($tmp == '`') {
                if (preg_match('/' . substr($key, 1, -1) . '/', $v)) {
                    $this->args[] = $v;

                    return $array[ $key ];
                }
            }
        }

        return null;
    }

}