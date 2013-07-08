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
$currentPost = null;

if(!empty($_GET['id'])) {
    $currentPost = new Post();
    $currentPost->load($_GET['id']);
}
else
{
    die('WTF R U DOING HERE BRO?');
}

if(!empty($currentPost))
{
    $post_type = $GLOBALS['BOT_CONFIG'][$currentPost->provider_id]['name'];
}
else
{
    die("Unable to load post");
}

if(!empty($_GET['hidden'])) {
    if($_GET['hidden'] == 1)
    {
        $currentPost->hidden = 1;
    }
    else
    {
        $currentPost->hidden = 0;
    }
    $rc = '';
    if($currentPost->save())
    {
        $rc = 'success';
    }
    die($rc);
}

if(!empty($_GET['delete']) && $_GET['delete'] == 1) {
    $rc = $currentPost->delete();
    if($rc === true)
    {
        $rc = 'success';
    }
    die($rc);
}

if(
   !empty($_POST['contents'])
)
{

    $currentPost->contents = $_POST['contents'];

    if(!empty($_POST['title']))
        $currentPost->title = $_POST['title'];

    if(!empty($_POST['tags']))
        $currentPost->tags = $_POST['tags'];
    if(!empty($_POST['custom_data']))
        $currentPost->custom_data = $_POST['custom_data'];
    if(!empty($_POST['hidden']))
        $currentPost->hidden = $_POST['hidden'];

    if($currentPost->save()) {
        $msg = $currentPost->id." has been updated.";
        Event::write($currentPost->provider_id,EVENT::E_SUCCESS,$msg);
    }
}

?>
<h1>Editing Post</h1>
<div class="msg"><?php echo $msg; ?></div>
<form action="" method="post">
    <ul class="input_form">
        <li>
            <label>Post Type:</label>
            <?php echo $post_type.' Last Update:'.$currentPost->update_time.' Hidden:'.$currentPost->hidden; ?>
        </li>

        <li>
            <label>Title:</label>
            <input type="text" name="title" value="<?php echo $currentPost->title ?>"/>
        </li>

        <li>
            <label>Content:</label>
            <div id="editor" style="width:75%; height:500px;"><?php  echo  htmlspecialchars($currentPost->contents); ?></div>
        </li>

        <li>
            <label>Tags:</label>
            <input type="text" name="tags" value="<?php echo $currentPost->tags; ?>"/>
        </li>

        <li>
            <label>Custom Data:</label>
            <div id="custom_data_editor" style="width:75%; height:250px;"><?php  echo $currentPost->custom_data?></div>
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

    contents.val(editor.getValue());

    editor.getSession().on('change', function(e) {
        preview.html(editor.getValue());
        contents.val(editor.getValue());
    });

    var custData = ace.edit("custom_data_editor");
    custData.setTheme("ace/theme/monokai");
    custData.getSession().setMode("ace/mode/json");
    var custom_data = jQuery('#custom_data');

    custom_data.val(custData.getValue());

    custData.getSession().on('change', function(e) {
        custom_data.val(custData.getValue());
    });

</script>