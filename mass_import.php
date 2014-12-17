<?php
/**
 * Created by PhpStorm.
 * User: heyqule
 * Date: 16/12/14
 * Time: 9:30 PM
 */
define('KEY','123123123');

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors',1);

require_once 'config.php';
require_once ROOT_PATH.DS.'includes'.DS.'BotRunner.php';

if($_GET['key'] == KEY)
{
    BotRunner::import($GLOBALS['BOT_CONFIG']);
}