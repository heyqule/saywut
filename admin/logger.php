<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once ROOT_PATH.DS.'includes'.DS.'Event.php';

Event::cleanup();
$arr = Event::read();

Core::getBots();


?>
<h1>View Log</h1>
<hr />
        <table style="width:100%;">
<?php
foreach($arr as $row):
    echo '<tr>'.
         '<td style="width:10%">'.$row['id'].'</td>'.
         '<td style="width:10%">'.Event::getBotName($GLOBALS['BOT_CONFIG'], $row['bot_id']).'</td>'.
         '<td style="width:10%">'.Event::getEventType($row['event_type']).'</td>'.
         '<td style="width:60%">'.$row['message'].'</td>'.
         '<td style="width:10%">'.$row['create_time'].'</td>'.
         '</tr>';
endforeach;
?>        
       </table>
