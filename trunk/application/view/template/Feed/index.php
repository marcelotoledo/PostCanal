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
        <tr id="feedaddoptions"><td colspan="2"></td></tr>
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

<div id="feedlistarea"></div>

</div>
