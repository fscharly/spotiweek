<?php

/**
* Basic $_SESSION usage wrapper
*/

namespace App;

class Session
{
    private static $_instance = null;

    private function __construct()
    {
        session_start();
    }

    public static function set($key, $value)
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new \App\Session();
        }
        $_SESSION[$key] = $value;
    }

    /**
    * Return $key stored in session, or $default_value if it doesn't exists.
    */
    public static function get($key, $default_value = false)
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new \App\Session();
        }
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return $default_value;
    }

}
