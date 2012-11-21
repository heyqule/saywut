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
                $bot = new $botInfo['class']($key,$botInfo);
                $bot->run();                
                file_put_contents(__DIR__.DIRECTORY_SEPARATOR.'run.log',$botInfo['class'].' - Success - '.date("Y-m-d H:i:s") ,FILE_APPEND);
            }
       } catch(Exception $e) {
            file_put_contents(__DIR__.DIRECTORY_SEPARATOR.'error.log',date("Y-m-d H:i:s")."\n----------\n".var_dump($e),FILE_APPEND);
       }
    }   
}

