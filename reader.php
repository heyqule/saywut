<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'config.php';
require_once SAYWUT_ROOT_PATH.DS.'includes'.DS.'Post_Collection.php';

//Get all providers
if(!empty($_REQUEST['providers'])) {
    $rc = array('All Posts');
    foreach($GLOBALS['BOT_CONFIG'] as $idx => $val)
    {
        if($idx > 0 && empty($GLOBALS['BOT_CONFIG'][$idx]['hidden']))
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

$showHidden = $_REQUEST['showHidden'];

$onlyHidden = $_REQUEST['onlyHidden'];

if(empty($offset)) {
    $offset = 0;
}

$post_collector = new \Saywut\Post_Collection();
if(!empty($provider))
{
    $post_collector->addWhere('provider_id','=',$provider)->addRaw('AND');
}

if(!empty($query)) {
    $tokens = explode(" ",$query);
    $first = true;

    $post_collector->addRaw('(');

    foreach($tokens as $idx => $val)
    {
        if($continue) $post_collector->addRaw('OR');
        $post_collector->addWhere('contents','LIKE','%'.$val.'%',$idx);
        $continue = true;
    }

    $post_collector->addRaw(')');
}

if(!empty($from)) {
    $post_collector->addWhere('create_time','>=',$from)->addRaw('AND');
}

if(!empty($to)) {
    $post_collector->addWhere('create_time','<=',$to)->addRaw('AND');
}


if(empty($showHidden)) {
    $post_collector->addMetaWhere('hidden','=',0);
}
elseif(!empty($onlyHidden))
{
    $post_collector->addMetaWhere('hidden','=',1);
}

$posts = $post_collector->loadByQuery($offset*10,10);

if(sizeof($posts) === 0) {
    echo '[:END:]';
    die;
}
//End Controller 

$rc = array();

foreach($posts as $post) {
    $tmp = new stdClass();
    $tmp->provider_class = strtolower($GLOBALS['BOT_CONFIG'][$post->provider_id]['class']);
    $tmp->provider = strtolower($GLOBALS['BOT_CONFIG'][$post->provider_id]['name']);
    $tmp->title = $post->title;
    $tmp->contents = $post->contents;
    $tmp->provider_cid = $post->provider_cid;

    if(!empty($post->meta))
    {
        $tmp->meta = $post->meta;
    }    
    $tmp->create_time = $post->create_time;
    $rc[] = $tmp;
}

echo json_encode($rc);