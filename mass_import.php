<?php
/**
 * Created by PhpStorm.
 * User: heyqule
 * Date: 16/12/14
 * Time: 9:30 PM
 */
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors',1);

require_once 'config.php';
require_once ROOT_PATH.DS.'includes'.DS.'BotRunner.php';

if(php_sapi_name() == 'cli')
{
    \Saywut\BotRunner::import($GLOBALS['BOT_CONFIG']);
}
else
{
    echo "fk off la";
}