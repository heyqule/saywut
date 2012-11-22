<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Helper {
    public static function log($filename,$content) {
        file_put_contents(__DIR__.DIRECTORY_SEPARATOR.$filename, $content, FILE_APPEND);
    }
}
