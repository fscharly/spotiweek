<?php

/**
* Basic $_SESSION usage wrapper
*/

namespace App;

class Session
{
    private static $_instance = false;

    private function __construct()
    {
        session_start();
    }

    public static function init()
    {
        if (self::$_instance === false) {
            self::$_instance = new \App\Session();
        }
    }

    /**
    * Store $value in $_SESSION[$key].
    * @param $key Key to set
    * @param $value Value to set
    */
    public static function set($key, $value)
    {
        self::init();
        $_SESSION[$key] = $value;
    }

    /**
    * Return the value of $key stored in session, or $default_value if it
    * doesn't exists.
    * @param $key Key to find in $_SESSION
    * @param $default_value Value returned if $key is not found
    * @return Value stored in $_SESSION or $default_value
    */
    public static function get($key, $default_value = false)
    {
        self::init();
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return $default_value;
    }
}
