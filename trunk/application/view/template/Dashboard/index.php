<?php if(count($this->blogs) == -1) : ?>

<div id="noblogmsg" class="b-dialog" style="display:none">
<?php echo $this->translation()->no_registered_blog ?>. <?php B_Helper::a(ucfirst($this->translation()->application_click_here), "blog", "add") ?> <?php echo $this->translation()->new_blog_instruction ?>.
<hr>
<div class="b-dialog-buttons">
<a class="b-dialog-close"><?php echo $this->translation()->application_close ?></a>
</div>
</div>

<?php else : ?>

<div class="dashboardcontainers" id="feedscontainer">
<h2><?php echo $this->translation()->application_feeds ?></h2>
<div class="containercontentarea">
</div>
<div class="containerfooter">&nbsp;
<a id="feedaddlnk"><?php echo $this->translation()->feed_add ?></a>
</div>
</div>

<div class="dashboardcontainers" id="newscontainer">
<h2><?php echo $this->translation()->application_feed_items ?></h2>
<div class="containercontentarea">
</div>
<div class="containerfooter">&nbsp;
<a id="feedviewlnk" item="" style="display:none"><?php echo $this->translation()->view_feed_item ?></a>
</div>
</div>

<div class="dashboardcontainers" id="queuecontainer">
<h2><?php echo $this->translation()->application_queue ?></h2>
<div class="containercontentarea">
</div>
<div class="containerfooter">&nbsp;
<span id="queuelnks" style="display:none">
<a id="queuepublnk" item=""><?php echo $this->translation()->publish_queue_item ?></a> |
<a id="queueeditlnk" item=""><?php echo $this->translation()->edit_queue_item ?></a> | 
<a id="queuedellnk" item=""><?php echo $this->translation()->delete_queue_item ?></a>
</div>
</div>
</div>

<div id="feedaddform" class="b-dialog" style="display:none">
<form>
    <h1><?php echo $this->translation()->feed_add_form_title ?></h1>
    <table>
        <tr id="feedaddurlrow">
        <th><?php echo $this->translation()->feed_add_url ?>:</th>
        <td><input type="text" name="feedaddurl" value=""></td>
        </tr>
        <tr id="feedaddoptions"><td colspan="2"></td></tr>
        <tr id="feedaddmessage" style="display:none">
        <th>&nbsp;</th>
        <td class="message"></td>
        </tr>
        <tr>
        <th>&nbsp;</th>
        <td class="buttons">
            <input name="feedaddcancel" type="button" value="<?php echo $this->translation()->application_cancel ?>" class="b-dialog-close">
            <input name="feedaddsubmit" type="button" value="<?php echo $this->translation()->application_submit ?>">
        </td>
        </tr>
    </table>
</form>
</div>

<div id="queueeditform" style="display:none">
    <form method="post" action="somepage">
        <textarea name="content" style="width:100%"></textarea>
    </form>
</div>
<script type="text/javascript">
tinyMCE.init({
    mode : "textareas",
    theme : "simple"
});
</script>

<?php endif ?>
