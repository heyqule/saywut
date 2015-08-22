<?php
/**
 * Created by JetBrains PhpStorm.
 * User: heyqule
 * Date: 7/7/13
 * Time: 9:46 AM
 * To change this template use File | Settings | File Templates.
 */
session_start();
if(empty($_SESSION['is_logged'])) {
    die();
}

require_once '../config.php';
require_once SAYWUT_ROOT_PATH.DS.'includes'.DS.'Event.php';

try
{
    if(!empty($_GET['l']))
    {
        require_once SAYWUT_ROOT_PATH.DS.'admin'.DS.$_GET['l'].'.php';
    }
}
catch(Exception $e)
{
    echo $e->getMessage();
}