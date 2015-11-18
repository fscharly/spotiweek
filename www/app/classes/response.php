<?php

/**
* Only used to JSON formated responses
*/

namespace App;

class Response
{
    /**
    * Echo given $data array json formated then exit to avoid <html> page
    * structuration.
    */
    public static function json_response($data)
    {
        header("Content-Type: application/json");
    	echo json_encode($data);
        exit();
    }
}
