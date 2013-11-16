<?php
/**
 * Created by JetBrains PhpStorm.
 * User: heyqule
 * Date: 7/3/13
 * Time: 11:47 PM
 * To change this template use File | Settings | File Templates.
 */

require_once ROOT_PATH.DS.'bots'.DS.'Raw_Bot.php';

$msg = '';

if(!empty($_POST['contents']) &&
   !empty($_POST['post_type'])
)
{
    $data = array();
    $temp = new stdClass();
    $temp->contents = $_POST['contents'];
    if(!empty($_POST['title']))
    {
        $temp->title = $_POST['title'];
    }
    if(!empty($_POST['provider_cid']))
    {
        $temp->provider_cid = $_POST['provider_cid'];
    }

    //Need more work
    /*
    if(!empty($_POST['custom_data']))
    {
        $temp->custom_data = $_POST['custom_data'];
    }
    */

    if(!empty($GLOBALS['BOT_CONFIG'][$_POST['post_type']]['hidden']))
    {
        $temp->meta = new stdClass();
        $temp->meta->hidden = 1;
    }
    else
    {
        $temp->meta = new stdClass();
        $temp->meta->hidden = 0;
    }


    $data[] = $temp;
    $bot = new Raw_Bot($_POST['post_type'],$data,true);
    if($bot->getError()) {
        $msg = print_r($bot->getError(),true);
        Event::write($_POST['post_type'],EVENT::E_ERROR,$msg);
    }
    else
    {
        $msg = $_POST['title']." has been saved";
    }
}


?>
<h1>Add New Post</h1>
<?php echo $msg; ?>
<form action="" method="post">
    <ul class="input_form">
        <li>
            <label>Post Type:</label>
            <select name="post_type">
                <?php
                foreach($GLOBALS['BOT_CONFIG'] as $key => $value ) {
                    if($value['class'] == 'Raw_Bot') {
                        echo '<option value="'.$key.'">'.$value['name'].'</option>';
                    }
                }
                ?>
            </select>
        </li>

        <li>
            <label>Title:</label>
            <input type="text" name="title" />
        </li>
        <li>
            <label>Provider CID:</label>
            <input type="text" name="provider_cid" />
        </li>
        <li>
            <label>Content:</label>
            <textarea id="contents" style="width:75%; height:500px;" name="contents"></textarea>
        </li>

        <li>

            <input type="submit" value="Submit" />
        </li>
    </ul>
</form>