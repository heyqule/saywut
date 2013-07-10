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
    if(!empty($_POST['tags']))
    {
        $temp->tags = $_POST['tags'];
    }
    if(!empty($_POST['custom_data']))
    {
        $temp->custom_data = $_POST['custom_data'];
    }

    if(!empty($GLOBALS['BOT_CONFIG'][$_POST['post_type']]['hidden']))
    {
        $temp->hidden = 1;
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
            <div id="custom_data_editor" style="width:75%; height:250px;"></div>
        </li>

        <li>
            <input type="hidden" id="contents" name="contents"/>
            <input type="hidden" id="custom_data" name="custom_data"/>
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

    var custData = ace.edit("custom_data_editor");
    custData.setTheme("ace/theme/monokai");
    custData.getSession().setMode("ace/mode/json");
    var custom_data = jQuery('#custom_data');

    custData.getSession().on('change', function(e) {
        custom_data.val(custData.getValue());
    });
</script>