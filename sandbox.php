<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors',1);

// require_once('bots/Twitter_Bot.php');
require_once('bots/libs/weibo/WeiboOAuthV2.php');

$appKey = '1191803748';
$appSecret = '7a6a04d39bf8f6834c0e5b117906c56a';
$oauthKey = '2.00UwBTuBGbgeSB71e24d0cdf0_uV25';

$connection = new WeiboOAuthV2($appKey,$appSecret,$oauthKey);

$redirect_uri = "http://www.heyqule.com/saywut/sandbox.php";

if(!empty($_REQUEST['code']))
{
    $keys = array();
    $keys['code'] = $_REQUEST['code'];
    $keys['redirect_uri'] = $redirect_uri;
    try {
        $token = $connection->getAccessToken( 'code', $keys ) ;
        print_r($token);
    } catch (OAuthException $e) {
    }
}
else
{
    $keys = array(
        'redirect_uri' => $redirect_uri
    );
    echo '<a href="'.$connection->getAuthorizeURL($redirect_uri).'">Login</a>';
}

?>