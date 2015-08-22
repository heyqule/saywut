<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if(empty($_SESSION['is_logged'])) {
    die();
}

require_once SAYWUT_ROOT_PATH.DS.'includes'.DS.'Event.php';

\Saywut\Event::cleanup();
$arr = \Saywut\Event::read();

\Saywut\Core::getBots();


?>
<h1>View Log</h1>
<hr />
        <table style="width:100%;">
<?php
foreach($arr as $row):
    echo '<tr>'.
         '<td style="width:10%">'.$row['id'].'</td>'.
         '<td style="width:10%">'.\Saywut\Core::getBotName($row['bot_id']).'</td>'.
         '<td style="width:10%">'.\Saywut\Event::getEventType($row['event_type']).'</td>'.
         '<td style="width:60%">'.$row['message'].'</td>'.
         '<td style="width:10%">'.$row['create_time'].'</td>'.
         '</tr>';
endforeach;
?>        
       </table>
