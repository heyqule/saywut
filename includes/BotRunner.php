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
                
                //Core::log('run.log',$botInfo['class'].' - Success - '.date("Y-m-d H:i:s")."\n");
            }
       } catch(Exception $e) {
             //Core::log('error.log',date("Y-m-d H:i:s")."\n----------\n".var_dump($e));
       }
    } 
    

}

