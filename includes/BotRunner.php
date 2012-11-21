<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class BotRunner 
{
    public static function init($botConfig) {
       try
       {
            foreach($botConfig as $key => $botInfo) {
                if($key == 0) {
                    //Skip placeholder bot;
                    continue;
                }

                require_once __DIR__.DIRECTORY_SEPARATOR.$botInfo['class'].'.php';
                $bot = new $botInfo['class']($key,$botInfo['account']);
                $bot->run();
            }
       } catch(Exception $e) {
            file_put_contents(__DIR__.DIRECTORY_SEPARATOR.'error.log',var_dump($e),FILE_APPEND);
       }
    }   
}

