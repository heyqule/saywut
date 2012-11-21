<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
</head>
<body>

<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors',1);

require_once('includes/Twitter_Bot.php');


echo 'WTF';

$lol = new Twitter_Bot(1);
$lol->setUserTimeline('heyqule');

$lol->run();
echo 'DONE';


require_once('includes/Post_Collection.php');
$p = new Post_Collection();
$haha = $p->loadDefault();

foreach($haha as $value) {
    echo '...'.$value->time.'...<br />';
}


?>
    </body>
</html>