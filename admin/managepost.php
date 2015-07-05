<?php
/**
 * Created by JetBrains PhpStorm.
 * User: heyqule
 * Date: 7/3/13
 * Time: 11:47 PM
 * To change this template use File | Settings | File Templates.
 */

require_once ROOT_PATH.DS.'includes'.DS.'Post.php';
require_once ROOT_PATH.DS.'includes'.DS.'Post_Collection.php';

$collect = new Post_Collection();

$limit = 50;
$offset = 0;

$page = 0;
if(isset($_GET['page']))
{
    $page = $_GET['page'];
}

$system_message = '';

if(isset($page))
{
    $offset = $page * $limit;
}

if(isset($_GET['reindexall'])) {
    $system_message = 'Reindex completed. Affected Rows: '.$collect->reindexAll();
}

$posts = $collect->addOrderBy('id')->loadByQuery($offset,$limit);
$size = $collect->getSize();
?>
<h2>System Options</h2>
<menu>
    <a href="?l=managepost&reindexall=1">Reindex for Fulltext Search</a>
</menu>
<div class="system_message">
    <?php echo $system_message; ?>
</div>
<h2>Pages (<?php echo $size ?> records)</h2>
<menu>
    Page:
    <?php
    for($i = 0; $i*$limit < $size; $i++) {
        if($i == $page)
            echo $i.' | ';
        else
            echo '<a href="?l=managepost&page='.$i.'">'.$i.'</a> | ';
    }
    ?>
</menu>
<table>
    <tr>
        <th style="width:10%">ID</th>
        <th style="width:15%">Info</th>
        <th style="width:5%">Provider</th>
        <th style="width:10%">Provider Post ID</th>
        <th style="width:40%">Contents</th>
        <th style="width:5%">Is Hidden</th>
    </tr>
    <?php foreach($posts as $value):?>
        <tr id="p-<?php echo $value->id; ?>">
            <td class="col_id"><?php echo $value->id; ?><br />
                <a href="?l=editpost&id=<?php echo $value->id; ?>">Edit</a> <br />
                <a href="#" class="hidden" data-id="<?php echo $value->id; ?>"><?php echo ($value->hidden) ? 'Unhide' : 'Hide'; ?></a> <br />
                <a href="#" class="delete" data-id="<?php echo $value->id; ?>">Delete</a>
            </td>
            <td class="col_infos"><?php echo $value->title.'<br />CD: '.$value->create_time.'<br />UD: '.$value->update_time;  ?></td>
            <td class="col_type"><?php echo Core::getBotName($value->provider_id); ?></td>
            <td class="col_type_cid"><?php echo $value->provider_cid; ?></td>
            <td class="col_content"><?php echo substr(strip_tags($value->contents),0,140); ?></td>
            <td class="col_hidden"><?php echo ($value->meta->hidden) ? 'true' : 'false'; ?></td>

        </tr>
    <?php endforeach; ?>
</table>

<script type="text/javascript">
$('.hidden').click(function() {
    var jThis = $(this);
    var hidden = -1;
    if(jThis.html() == 'Hide') {
        hidden = 1;
    }
    var args = [jThis.data('id'),hidden];
    requestAction(
        {'l':'editpost','id':jThis.data('id'),'hidden':hidden},
        function(args) {
            var jThis = $('#p-'+args[0]);
            if(args[1] == 1) {
                jThis.find('.hidden').html('Unhide');
                jThis.find('.col_hidden').html('true');
            }
            else
            {
                jThis.find('.hidden').html('Hide');
                jThis.find('.col_hidden').html('false');
            }
        },
        args
    );
});

$('.delete').click(function() {
    var jThis = $(this);
    var rowId = jThis.data('id');
    if(confirm('Are you sure you want to delete Post:'+rowId+'?'))
    {
        requestAction(
            {'l':'editpost','id':rowId,'delete':1},
            function(rowId) {
                $('#p-'+rowId).remove();
            },
            rowId
        );
    }
});

function requestAction(data,successAction,args) {
    $.get(
        'ajax.php',
        data,
        function(rcdata) {
            if(rcdata == 'success')
            {
                successAction(args);
            }
            else
            {
                alert('failed!');
            }
        }
    );
}
</script>
<style>
    tr:nth-child(odd) {
        background: #eee;
    }
</style>