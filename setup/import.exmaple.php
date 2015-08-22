<?php
/**
 * Created by JetBrains PhpStorm.
 * User: heyqule
 * Date: 10/16/13
 * Time: 11:32 PM
 * To change this template use File | Settings | File Templates.
 */
error_reporting(E_ALL | E_STRICT);
ini_set('display_error',1);


require_once '../config.php';
require_once SAYWUT_ROOT_PATH.DS.'includes'.DS.'Post_Collection.php';


try
{

$mysql = new PDO('mysql:host='.MYSQL_DB_HOST.';port='.MYSQL_DB_PORT.';dbname='.MYSQL_DB_NAME.';',MYSQL_DB_USER,MYSQL_DB_PASS);

$bot_insert = "INSERT IGNORE INTO `posts_bots` (id, class, name) VALUES (:id, :class, :name);";

$bot_config_exec = $mysql->prepare($bot_insert);

echo 'begin<br />';
$mysql->beginTransaction();

foreach($GLOBALS['BOT_CONFIG'] as $key => $bot) {
    if($key != 0)
    {
        $bot_config_exec->execute(array(
           ':id'=>$key,
           ':class'=>$bot['class'],
           ':name'=>$bot['name']
        ));
    }
}
$mysql->commit();

define('DB_PATH', 'something.sqlite');
define('DB_USER','something');
define('DB_PASS','something');

$sqlite = new PDO('sqlite:'.SAYWUT_ROOT_PATH.DS.DB_PATH,DB_USER,DB_PASS);

$sth = $sqlite->prepare('SELECT * FROM posts;');
$sth->execute();
$rows = $sth->fetchAll();

$posts = array();

foreach($rows as $row) {
    $post = new Post();
    $post->id = $row['id'];
    $post->title = $row['title'];
    $post->contents = $row['contents'];
    $post->provider_id = $row['provider_id'];
    $post->provider_cid = $row['provider_cid'];
    $post->tags = $row['tags'];
    $post->custom_data = $row['custom_data'];
    $post->time = $row['time'];
    $post->hidden = $row['hidden'];
    $post->update_time = $row['update_time'];
    $posts[$post->id] = $post;
}

$post_insert = '
INSERT INTO `posts`
 (`id`, `title`, `provider_id`, `provider_cid`, `contents`, `create_time`, `update_time`) VALUES
 (:id, :title, :provider_id, :provider_cid, :contents, :create_time, :update_time);
';

$meta_insert = '
INSERT INTO `posts_meta` (`post_id`,`meta_name`,`meta_value`) VALUES (:post_id,:meta_name,:meta_value);
';

$post_exec = $mysql->prepare($post_insert);
$meta_exec = $mysql->prepare($meta_insert);

$mysql->beginTransaction();
foreach($posts as $post) {

    if(empty($post->provider_cid)) {
        $post->provider_cid = uniqid();
    }

    $post_exec->execute(array(
        ':id'=>null,
        ':title'=>$post->title,
        ':provider_id'=>$post->provider_id,
        ':provider_cid'=>$post->provider_cid,
        ':contents'=>$post->contents,
        ':create_time'=>$post->time,
        ':update_time'=>$post->update_time
    ));
    $id = $mysql->lastInsertId();

    $meta_exec->execute(array(
        ':post_id'=>$id,
        ':meta_name'=>'hidden',
        ':meta_value'=>$post->hidden
    ));

    $custom_data = json_decode($post->custom_data);

    foreach($custom_data as $key => $value) {
        $meta_exec->execute(array(
            ':post_id'=>$id,
            ':meta_name'=>$key,
            ':meta_value'=>$value
        ));
    }


    echo $post->id.' added to '.$id.'<br />';
    print_r($post_exec->errorInfo());
    echo '<br /><br />';
}
$mysql->commit();

} catch(Exception $e) {
    $mysql->rollBack();
    echo '<br />ERROR:<br />';
    echo nl2br(print_r($e,true));
}