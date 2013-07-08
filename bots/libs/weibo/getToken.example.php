<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors',1);

require_once('WeiboOAuthV2.php');

$appKey = '1191803748';
$appSecret = '7a6a04d39bf8f6834c0e5b117906c56a';

$connection = new WeiboOAuthV2($appKey,$appSecret);

$redirect_uri = "http://www.heyqule.com/saywut/bots/libs/weibo/getToken.php";

if(!empty($_REQUEST['code']))
{
    $keys = array();
    $keys['code'] = $_REQUEST['code'];
    $keys['redirect_uri'] = $redirect_uri;
    try {
        $token = $connection->getAccessToken( 'code', $keys );
        echo 'TOKEN:<br />';
        print_r($token);
        $arr = array(
            'access_token'=>$token['access_token']
        );
        echo '<br />TOKEN INFO:<br />';
        $token_info = $connection->post('oauth2/get_token_info',$arr);
        print_r($token_info);

        $token_info = $connection->get('oauth2/get_token_info',$arr);
        print_r($token_info);

        echo '<br />USER Timeline:<br />';
        $token_info = $connection->get('statuses/user_timeline',$arr);
        print_r($token_info);
    } catch (OAuthException $e) {
    }
}
else
{
    $keys = array(
        'redirect_uri' => $redirect_uri
    );
    echo '<a href="'.$connection->getAuthorizeURL($redirect_uri).'">Login / Refresh Token</a>';
}

?>