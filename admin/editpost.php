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

    if(empty($currentPost->meta))
    {
        $currentPost->meta = new stdClass();
    }

    if($_GET['hidden'] == 1)
    {
        $currentPost->meta->hidden = 1;
    }
    else
    {
        $currentPost->meta->hidden = 0;
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

if(!empty($_POST['contents']))
{

    $currentPost->contents = $_POST['contents'];

    if(!empty($_POST['title']))
        $currentPost->title = $_POST['title'];

    if(!empty($_POST['provider_cid']))
        $currentPost->provider_cid = $_POST['provider_cid'];

    if(!empty($_POST['keywords'])) {
        $currentPost->keywords = $_POST['keywords'];
    }

    if(!empty($_POST['create_time'])) {
        $currentPost->create_time = date(DT_FORMAT,strtotime($_POST['create_time']));
    }

    $meta_name = $_POST['meta_name'];
    $meta_value = $_POST['meta_value'];

    foreach($meta_name as $key => $value) {
        $currentPost->setMeta($value, $meta_value[$key]);
    }


    if($currentPost->save()) {
        $msg = $currentPost->id." has been updated.";
    }
    else
    {
        $msg = $currentPost->id." update failed.";
    }
}

?>
<h1>Editing Post</h1>
<div class="msg"><?php echo $msg; ?></div>
<form action="" method="post">
    <ul class="input_form">
        <li>
            Post Type:
            <?php echo $post_type.' <br /> Last Update: '.$currentPost->update_time; ?>
        </li>

        <li>
            <label>Title:</label>
            <input type="text" name="title" value="<?php echo $currentPost->title ?>"/>
        </li>
        <li>
            <label>Provider CID:</label>
            <input type="text" name="provider_cid" value="<?php echo $currentPost->provider_cid ?>" />
        </li>
        <li>
            <label>Content:</label>
            <textarea id="contents" style="width:75%; height:500px;" name="contents"><?php  echo $currentPost->contents; ?></textarea>
        </li>
        <li>
            <label>Keywords:</label>
            <input type="text" name="keywords" value="<?php echo $currentPost->keywords ?>" />
        </li>
        <li>
            <label>Create Date:</label>
            <input id="create_time" name="create_time" value="<?php echo $currentPost->create_time; ?>" />
        </li>
    </ul>
    <h2>Meta Fields</h2>
    <ul class="meta_fields">
    <?php
        if($currentPost->meta):
        foreach($currentPost->meta as $key => $value):
    ?>
        <li><div class="col1"><label>Meta Name</label><input type="text" name="meta_name[]" value="<?php echo $key ?>" /></div><div class="col2"><label>Meta Value</label><textarea name="meta_value[]" /><?php echo $value ?></textarea></div></li>
    <?php endforeach; endif; ?>
    </ul>
    <button type="button" class="new_meta">New Meta Field</button>
    <br /><br />
    <button type="submit">Submit</button>
</form>

<script type="text/javascript">
    $('.new_meta').click(function(e) {
        $('.meta_fields').append('<li><div class="col1"><label>Meta Name</label><input type="text" name="meta_name[]" /></div><div class="col2"><label>Meta Value</label><textarea name="meta_value[]" /></textarea></div></li>');
        e.preventDefault();
    });

</script>

