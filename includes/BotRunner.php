<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once  ROOT_PATH.DS.'includes'.DS.'Core.php';

class BotRunner 
{
    public static function init($botConfig)
    {
       try
       {
            foreach($botConfig as $key => $botInfo)
            {
                if($key == 0)
                {
                    //Skip placeholder bot;
                    continue;
                }

                require_once ROOT_PATH.DS.'bots'.DS.$botInfo['class'].'.php';

                $bot = new $botInfo['class'](Core::getBotKey($botInfo),$botInfo);
                $bot->run();
            }
       }
       catch(Exception $e)
       {
            Event::write(0,Event::E_ERROR,$e->getMessage().' @ '.$e->getFile().' L: '.$e->getLine());
       }
    }

    public static function import($botConfig,$botId = 0)
    {
        try
        {
            if($botId && is_int($botId))
            {
                $config = $botConfig[$botId];

                require_once ROOT_PATH.DS.'bots'.DS.$config['class'].'.php';

                $bot = new $config['class'](Core::getBotKey($config),$config);
                if(method_exists($bot,'import'))
                {
                    $bot->import();
                }
                return;
            }
            elseif($botId)
            {
                throw new Exception('Invalid Bot Id');
            }



            foreach($botConfig as $key => $botInfo)
            {
                if($key == 0)
                {
                    //Skip placeholder bot;
                    continue;
                }

                require_once ROOT_PATH.DS.'bots'.DS.$botInfo['class'].'.php';

                $bot = new $botInfo['class'](Core::getBotKey($botInfo),$botInfo);
                if(method_exists($bot,'import'))
                {
                    $bot->import();
                }
            }
        }
        catch(Exception $e)
        {
            Event::write(0,Event::E_ERROR,$e->getMessage().' @ '.$e->getFile().' L: '.$e->getLine());
        }

    }
}

