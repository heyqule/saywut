<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include 'includes/Post_Collection.php';

$offset = $_REQUEST['offset'];
$time_from = $_REQUEST['time_from'];
$time_to = $_REQUEST['time_to'];
$mood_id = $_REQUEST['provider_id'];

if(empty($offset)) {
    $offset = 0;
}

$post_collector = new Post_Collection();
$posts = $post_collector->loadDefault($offset);

if(sizeof($posts) === 0) {
    echo '[:END:]';
}
//End Controller 

$rc = array();

foreach($posts as $post) {
    $tmp = new stdClass();
    $tmp->provider = strtolower($BOT_CONFIG[$post->provider_id]['name']);
    $tmp->title = $post->title;
    $tmp->contents = $post->contents;
    $tmp->provider_cid = $post->provider_cid;
    $tmp->time = $post->time;
    $rc[] = $tmp;
}

echo json_encode($rc);