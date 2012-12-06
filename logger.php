<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors',1);

require_once 'config.php';
require_once ROOT_PATH.DS.'includes'.DS.'Event.php';

$arr = Event::read();

?>

<html>
    <head></head>
    <body>
        <table style="width:800px;">
<?php
foreach($arr as $row) {
    echo '<tr>'.
         '<td>'.$row['id'].'</td>'.
         '<td>'.Event::getBotName($BOT_CONFIG, $row['bot_id']).'</td>'.
         '<td>'.Event::getEventType($row['event_type']).'</td>'.   
         '<td>'.$row['message'].'</td>'.   
         '<td>'.$row['time'].'</td>'.
         '</tr>';
}
?>        
       </table>
    </body>
</html>
