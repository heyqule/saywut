<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors',1);

require_once 'config.php';
require_once ROOT_PATH.DS.'includes'.DS.'BotRunner.php';

BotRunner::init($BOT_CONFIG);
?>
