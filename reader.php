<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'config.php';
require_once ROOT_PATH.DS.'includes'.DS.'Post_Collection.php';

//Get all providers
if(!empty($_REQUEST['providers'])) {
    $rc = array('All Posts');
    foreach($GLOBALS['BOT_CONFIG'] as $idx => $val)
    {
        if($idx > 0)
        {
            $rc[$idx] = $val['name'];
        }
    }
    echo json_encode($rc);
    die;
}

//Get Post
$offset = $_REQUEST['offset'];
$from = $_REQUEST['fromDate'];
$to = $_REQUEST['toDate'];
$provider = $_REQUEST['provider'];
$query = $_REQUEST['query'];

if(empty($offset)) {
    $offset = 0;
}

$post_collector = new Post_Collection();
$posts = $post_collector->loadByQuery($provider,$query,$from,$to,$offset,10);

if(sizeof($posts) === 0) {
    echo '[:END:]';
    die;
}
//End Controller 

$rc = array();

foreach($posts as $post) {
    $tmp = new stdClass();
    $tmp->provider = strtolower($GLOBALS['BOT_CONFIG'][$post->provider_id]['name']);
    $tmp->title = $post->title;
    $tmp->contents = $post->contents;
    $tmp->provider_cid = $post->provider_cid;
    if(!empty($post->tags))
    {
        $tmp->tags = $post->tags;
    }
    if(!empty($post->custom_data))
    {
        $tmp->custom_data = $post->custom_data;
    }    
    $tmp->time = $post->time;
    $rc[] = $tmp;
}

echo json_encode($rc);