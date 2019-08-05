<?php
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/7/30
 * Time: 17:17
 */
namespace Snake\Http;

class Router {

    private static $info = [];
    private static $as_info = [];
    private static $group_info = [];
    private static $max_group_depth = 200;

    private static function withGroupAction($group_info, $action){
        if (is_array($action)) {
            if (isset($group_info['as']) && isset($action['as']) ){
                $action['as'] = trim($group_info['as'], '.').'.'.$action;
            }
            if (isset($group_info['namespace'])) {
                $action['use'] = '\\'.$group_info['namespace'].'\\'.trim($action['use'], '\\');
            }
            if (isset($group_info['middle'])){
                if (!isset($action['middle'])){
                    $action['middle'] = [];
                }
                $action['middle'] = array_merge($action['middle'], array_reverse($group_info['middle']));
            }
            if(isset($group_info['cache'])){
                $action['cache'] = $group_info['cache'];
            }
        }else{
            if (isset($group_info['namespace'])){
                $action = '\\'. $group_info['namespace'] . '\\'. trim($action, '\\');
            }
            $action = ['use' => $action, 'middle'=> []];
            if (isset($group_info['middle'])){
                $action['middle'] = array_merge($action['middle'], array_reverse($group_info['middle']));
            }
            if (isset($group_info['cache'])){
                $action['cache'] = $group_info['cache'];
            }
        }
        return $action;
    }

    private static function withGroupPath($group_info, $path){
        $path = '/'. trim($path, '/');
        if (isset($group_info['prefix'])){
            $prefix = trim($group_info['prefix'], '/');
            $path = '/'.trim($prefix, '/').$path;
        }
        return $path;
    }


    private static function createAsInfo($path, $action){
        if (isset($action['as'])){
            self::$as_info[$action['as']] = rtrim($path, '/');
        }
    }

    private static function setPath($array, $value, $i = 0){
        if (isset($array[$i])){
            if (is_numeric($array[$i])){
                $array[$i] = '#'. $array[$i];
            }else if($array[$i] == ''){
                $array[$i] = 0;
            }
            return [$array[$i] => self::setPath($array,$value, $i+1)];
        } else{
            return $value;
        }
    }

    public static function set($method, $path, $action){
        foreach (self::$group_info as $value){
            $action = self::withGroupAction ($value, $action);
            $path = self::withGroupPath($value, $path);
        }
        if (is_array($action)) {
            self::createAsInfo($path, $action);
        }
        $array = explode('/', $method.$path);
        if (is_array($action)) {
            $value = end($array);
            if($value !== ''){
                $array[] = '';
            }
        }
        self::$info = array_merge_recursive(self::$info, self::setPath($array, $action));
    }

    public static function get($path, $action) {
        self::set('get', $path, $action);
    }

    public static function post($path, $action){
        self::set('post', $path, $action);
    }

    public static function put($path, $action){
        self::set('put', $path, $action);
    }

    public static function delete($path, $action){
        self::set('delete', $path, $action);
    }

    public static function patch($path, $action){
        self::set('patch', $path, $action);
    }

    public static function any($path, $action){
        self::get($path, $action);
        self::post($path, $action);
        self::put($path, $action);
        self::delete($path, $action);
        self::patch($path, $action);
    }




}