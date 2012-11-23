<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once  __DIR__.DIRECTORY_SEPARATOR.'Helper.php';

class Helper {
    public static function log($filename,$content) {
        file_put_contents(__DIR__.DIRECTORY_SEPARATOR.$filename, $content, FILE_APPEND);
    }
}
