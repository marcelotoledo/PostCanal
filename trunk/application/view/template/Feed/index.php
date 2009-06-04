<?php if(count($this->blogs) > 0) : ?>

<br/>
<div id="subcontainer">

<h1><?php echo $this->translation()->feeds ?></h1>
<div id="feedlnkdiv">
    <a href="#" id="feedaddlnk"><?php echo $this->translation()->feed_add ?></a>
    <a href="#" id="feedimportlnk"><?php echo $this->translation()->feed_import ?></a>
</div>

<form id="feedaddform" style="display:none">
    <table>
        <tr id="feedaddurlrow">
        <th><?php echo $this->translation()->feed_add_url ?>:</th>
        <td><input type="text" id="feedaddurl" value=""></td>
        </tr>
        <tr id="feedaddoptions"><td colspan="2">
        </td></tr>
        <tr id="feedaddmessage" style="display:none">
        <th>&nbsp;</th>
        <td class="message"></td>
        </tr>
        <tr>
        <th>&nbsp;</th>
        <td class="buttons">
            <input id="feedaddcancel" type="button" value="<?php echo $this->translation()->cancel ?>">
            <input id="feedaddsubmit" type="button" value="<?php echo $this->translation()->submit ?>">
        </td>
        </tr>
    </table>
</form>

<div id="feedoptionblank" style="display:none">
    <div class="feedoptionbutton">
        <input name="feedaddoption" type="radio" url="">
    </div>
    <div class="feedoptiontitle">
    </div>
    <div style="clear:left"></div>
</div>

<form id="feedimportform" enctype="multipart/form-data" action="<?php B_Helper::url('feed','index') ?>" method="POST" style="display:none">
    <table>
        <tr>
        <th><?php echo $this->translation()->feed_import_file ?>:</th>
        <td><input type="file" name="feedimportfile" value=""></td>
        <tr>
        <th>&nbsp;</th>
        <td class="buttons">
            <input id="feedimportcancel" type="button" value="<?php echo $this->translation()->cancel ?>">
            <input id="feedimportsubmit" type="submit" value="<?php echo $this->translation()->submit ?>">
        </td>
        </tr>
    </table>
</form>

<div id="feedlistarea">
    <div id="feeditemblank" style="display:none">
        <div class="feeditemleft">
            <div class="feeditemtitle"></div>
            <div class="feeditemurl"></div>
        </div>
        <div class="feeditemright">
            <a href="#" class="feedrenamelnk" feed="blank"><?php echo $this->translation()->rename ?></a>
            <a href="#" class="feedtogglelnk" feed="blank"><?php echo $this->translation()->disable ?></a>
            <a href="#" class="feeddeletelnk" feed="blank"><?php echo $this->translation()->delete ?></a>
        </div>
        <div style="clear:both"></div>
    </div>
</div>

</div>

<?php endif ?>
