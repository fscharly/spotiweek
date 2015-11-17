<?php

namespace App;

class Config
{
    const CONFIG_FOLDER = '../config/';

    private static $_instance;

    private $_config_content;

    private function __construct()
    {
        $folder = dirname(__FILE__).'/'.self::CONFIG_FOLDER;
        $this->_config_content = array();
        if ($handle = opendir($folder)) {
            while (($entry = readdir($handle)) !== false) {
                $path = $folder.$entry;
                if (is_file($path) && $path != '.' && $path != '..') {
                    $data = file_get_contents($path);
                    $this->_config_content = array_merge($this->_config_content, json_decode($data, true));
                }
            }
            closedir($handle);
        }
    }

    public static function get($key)
    {
        if (!isset($_instance)) {
            $_instance = new \App\Config();
        }
        $path = explode('.', $key);
        return $_instance->getValue($path);
    }

    private function getValue($path)
    {
        $config = $this->_config_content;
        foreach ($path as $p) {
            $config = $config[$p];
        }
        return $config;
    }
}
