<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once ROOT_PATH.DS.'config.php';

final class Core {        
    
    protected static $db_res;
    
    public static function getDBHandle() {
        if(static::$db_res == null) {
            static::$db_res = new PDO('sqlite:'.DB_PATH,DB_USER,DB_PASS);
        }        
        return static::$db_res;
    }        
}
