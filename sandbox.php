<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors',1);

require_once('config.php');
require_once('bots/Raw_Bot.php');

print_r(Core::getMetaTags('http://www.heyqule.com'));


echo 'MEMORY:'.memory_get_usage();
?>