<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors',1);

require_once('config.php');
require_once('bots/Raw_Bot.php');
/*
require_once('bots/libs/weibo/WeiboOAuthV2.php');

$appKey = '1191803748';
$appSecret = '7a6a04d39bf8f6834c0e5b117906c56a';
$oauthKey = '2.00UwBTuBGbgeSB71e24d0cdf0_uV25';

$connection = new WeiboOAuthV2($appKey,$appSecret,$oauthKey);

*/
echo '---------------------------------<br />';
echo 'LOAD TEST<br />';
echo '---------------------------------<br /><br />';

$post = new Post();

$post->load(205);

print_r($post);

echo '<br /><br />';
echo '----------------------------------<br />';
echo 'Save Test<br />';
echo '----------------------------------<br /><br />';
$post->title="123456";
$post->save();

$post2 = new Post();

$post2->load(205);

echo $post2->title;

$post2->title = '';
$post2->id = null;
$post2->save();

echo '<br /><br />';
echo '----------------------------------<br />';
echo 'Delete Test<br />';
echo '----------------------------------<br /><br />';

$post2->delete();

$post3 = new Post();
$post3->load(205);
echo "TTT".$post3->title;

$post2->save();

echo '<br /><br />';
echo '----------------------------------<br />';
echo 'Collection Test<br />';
echo '----------------------------------<br /><br />';

$post_collector = new Post_Collection();
$post_collector->addMetaWhere('hidden','=',1);
$posts = $post_collector->loadByQuery(0,10);

print_r($posts);

echo '<br /><br />';
echo '----------------------------------<br />';
echo 'Core::getBotKey:'.Core::getBotKey($GLOBALS['BOT_CONFIG'][1]).' - '.Core::getBotKey($GLOBALS['BOT_CONFIG'][12]).'<br />';
echo '-----------------------------------<br /><br />';


echo 'MEMORY:'.memory_get_usage();
?>