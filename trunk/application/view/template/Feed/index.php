<div class="subcontainer">

<h1><?php echo $this->translation()->feeds ?></h1>
<div id="feedaddlnkdiv">
    <a id="feedaddlnk"><?php echo $this->translation()->feed_add ?>
</div>

<form>
    <table id="feedaddformtable" style="display:none">
        <tr id="feedaddurlrow">
        <th><?php echo $this->translation()->feed_add_url ?>:</th>
        <td><input type="text" name="feedaddurl" value=""></td>
        </tr>
        <tr id="feedaddoptions"><td colspan="2">
            <div class="feedoption" id="feedoptionblank">
                <div class="feedoptionbutton">
                    <input name="feedaddoption" type="radio" url="">
                </div>
                <div class="feedoptiontitle">
                </div>
                <div style="clear:left"></div>
            </div>
        </td></tr>
        <tr id="feedaddmessage" style="display:none">
        <th>&nbsp;</th>
        <td class="message"></td>
        </tr>
        <tr>
        <th>&nbsp;</th>
        <td class="buttons">
            <input name="feedaddcancel" type="button" value="<?php echo $this->translation()->application_cancel ?>">
            <input name="feedaddsubmit" type="button" value="<?php echo $this->translation()->application_submit ?>">
        </td>
        </tr>
    </table>
</form>

<div id="feedlistarea">
    <div class="feeditem" feed="blank" ord="0" style="display:none">
        <div class="feeditemleft">
            <div class="feeditemtitle"></div>
            <div class="feeditemurl"></div>
        </div>
        <div class="feeditemright">
            <a class="feedrenamelnk" feed="blank"><?php echo $this->translation()->rename ?></a>
            <a class="feedtogglelnk" feed="blank"><?php echo $this->translation()->disable ?></a>
            <a class="feeddeletelnk" feed="blank"><?php echo $this->translation()->delete ?></a>
        </div>
        <div style="clear:both"></div>
    </div>
</div>

</div>
