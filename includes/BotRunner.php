<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once  ROOT_PATH.DS.'includes'.DS.'Core.php';

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

                require_once ROOT_PATH.DS.'bots'.DS.$botInfo['class'].'.php';                
                
                $bot = new $botInfo['class']($key,$botInfo);
                $bot->run();
            }
       } catch(Exception $e) {
            Event::write(0,Event::E_ERROR,$e->getMessage().' @ '.$e->getFile().' L: '.$e->getLine());
       }
    } 
    

}

