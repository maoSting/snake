<?php
static $_snake_config = null;
/**
 * Created by PhpStorm.
 * Author: DQ
 * Date: 2019/7/31
 * Time: 15:25
 */
if (!function_exists('config_set')) {
    /**
     *
     * @param $key
     * @param $value
     *
     * @return mixed
     * Author: DQ
     */
    function config_set($key, $value) {
        global $_snake_config;
        $_snake_config[ $key ] = $value;

        return $value;
    }
}

if (!function_exists('config_import')) {
    /**
     * 导入配置类
     *
     * @param $path 文件名全路径
     *              Author: DQ
     */
    function config_import($path) {
        if (!is_file($path)) {
            return false;
        }
        $key   = basename($path, '.php');
        $value = include $path;
        config_set($key, $value);

        return true;
    }
}

if (!function_exists('config_get')) {
    /**
     * 获取配置
     *
     * @param string $key 支持.语法
     * @param string $default
     *
     * @return null|string
     * Author: DQ
     */
    function config_get($key = '', $default = "") {
        global $_snake_config;
        if (strpos($key, '.') === false) {
            return isset($_snake_config[ $key ]) ? $_snake_config[ $key ] : $default;
        }
        $rst   = explode('.', $key);
        $array = $_snake_config;
        foreach ($rst as $val) {
            if (isset($array[ $val ])) {
                $array = $array[ $val ];
            } else {
                $array = $default;
            }
        }

        return $array;
    }
}

if (!function_exists('array_get_value_by_key')) {
    /**
     * 获取配置
     *
     * @param string $key 支持.语法
     * @param string $default
     *
     * @return null|string
     * Author: DQ
     */
    function array_get_value_by_key($array, $key, $default = null) {
        if (strpos($key, '.') === false) {
            return isset($array[ $key ]) ? $array[ $key ] : $default;
        }
        $rst = explode('.', $key);
        foreach ($rst as $val) {
            if (isset($array[ $val ])) {
                $array = $array[ $val ];
            } else {
                $array = $default;
            }
        }

        return $array;
    }
}

if (!function_exists('array_get_value_by_keys')) {
    /**
     *
     * @param       $array
     * @param array $keys
     *                   一维数组
     * @param null  $default
     *
     * @return mixed
     * Author: DQ
     */
    function array_get_value_by_keys($array, $keys = [], $default = null) {
        foreach ($keys as $key) {
            $tmp = array_get_value_by_key($array, $key);
            if ($tmp !== null) {
                return $tmp;
            }
        }

        return null;
    }
}

if (!function_exists('call')) {
    function call($function, $args) {
        if (strpos($function, '@' !== false)) {
            $ctr = explode('@', $function);

            return call_user_func_array([new $ctr[0], $ctr[1]], $args);
        } else {
            return call_user_func_array($function, $args);
        }
    }
}



