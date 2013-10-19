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

if(
   !empty($_POST['contents'])
)
{

    $currentPost->contents = $_POST['contents'];

    if(!empty($_POST['title']))
        $currentPost->title = $_POST['title'];



    if($currentPost->save()) {
        $msg = $currentPost->id." has been updated.";
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
            <textarea id="contents" style="width:75%; height:500px;" name="contents"><?php  echo  htmlspecialchars($currentPost->contents); ?></textarea>
        </li>

        <li>

            <input type="submit" value="Submit" />
        </li>
    </ul>
</form>
<h4>Preview</h4>
<hr />
<div class="preview">

</div>
