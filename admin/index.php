<?php
/**
 * Created by JetBrains PhpStorm.
 * User: heyqule
 * Date: 7/3/13
 * Time: 12:20 AM
 * To change this template use File | Settings | File Templates.
 */
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors',1);

require_once '../config.php';
require_once ROOT_PATH.DS.'includes'.DS.'Event.php';

session_start();
if(empty($_SESSION['is_logged'])) {
    header( 'Location: login.php' ) ;
}

?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="main.css"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script type="text/javascript" src="http://code.jquery.com/jquery-2.0.0.min.js"></script>
    <script type="text/javascript" src="plugins.js"></script>
</head>
<body>
<ul class="sidebar">
    <li>
        <a href="?l=addpost">Add A RawText Post</a>
    </li>
    <li>
        <a href="?l=managepost">Manage Posts</a>
    </li>
    <li>
        <a href="?l=logger">View Log</a>
    </li>
</ul>
<div class="content">
<?php
try
{
    if(!empty($_GET['l']))
    {
        require_once ROOT_PATH.DS.'admin'.DS.$_GET['l'].'.php';
    }
}
catch(Exception $e)
{
    echo $e->getMessage();
}
?>
</div>
</body>
</html>