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

if(!empty($_POST['title']) &&
   !empty($_POST['contents']) &&
   !empty($_POST['post_type'])
)
{
    $data = array();
    $temp = new stdClass();
    $temp->title = $_POST['title'];
    $temp->contents = $_POST['contents'];
    if(!empty($_POST['tags']))
        $temp->tags = $_POST['tags'];
    if(!empty($_POST['custom_data']))
        $temp->custom_data = $_POST['custom_data'];
    $data[] = $temp;
    $bot = new Raw_Bot($_POST['post_type'],$data,true);
    if($bot->getError()) {
        $msg = print_r($bot->getError(),true);
        Event::write($_POST['post_type'],EVENT::E_SUCCESS,$msg);
    }
    else
    {
        $msg = $_POST['title']." has been saved";
        Event::write($_POST['post_type'],EVENT::E_SUCCESS,$msg);
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
            <label>Content:</label>
            <div id="editor" style="width:75%; height:500px;"></div>
        </li>

        <li>
            <label>Tags:</label>
            <input type="text" name="tags" />
        </li>

        <li>
            <label>Custom Data:</label>
            <input type="text" name="custom_data" />
        </li>

        <li>
            <input type="hidden" id="contents" name="contents"/>
            <input type="submit" value="Submit" />
        </li>
    </ul>
</form>
<h4>Preview</h4>
<hr />
<div class="preview">

</div>

<script type="text/javascript">
    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/monokai");
    editor.getSession().setMode("ace/mode/html");
    var preview = jQuery('.preview');
    var contents = jQuery('#contents');

    editor.getSession().on('change', function(e) {
        preview.html(editor.getValue());
        contents.val(editor.getValue());
    });
</script>