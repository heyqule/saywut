<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors',1);

require_once 'config.php';
require_once ROOT_PATH.DS.'includes'.DS.'Event.php';

Event::cleanup();
$arr = Event::read();

?>

<html>
    <head></head>
    <body>
        <table style="width:100%;">
<?php
foreach($arr as $row) {
    echo '<tr>'.
         '<td style="width:10%">'.$row['id'].'</td>'.
         '<td style="width:10%">'.Event::getBotName($GLOBALS['BOT_CONFIG'], $row['bot_id']).'</td>'.
         '<td style="width:10%">'.Event::getEventType($row['event_type']).'</td>'.
         '<td style="width:60%">'.$row['message'].'</td>'.
         '<td style="width:10%">'.$row['time'].'</td>'.
         '</tr>';
}
?>        
       </table>
    </body>
</html>
