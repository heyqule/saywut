<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors',1);

require_once __DIR__.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'config.php';
require_once __DIR__.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'BotRunner.php';

BotRunner::init($BOT_CONFIG);
?>
