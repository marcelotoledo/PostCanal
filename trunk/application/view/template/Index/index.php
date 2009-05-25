<div id="logo">
    <h1>POSTCANAL</h1>
</div>
<div id="form">
    <form>
        <div id="ftitlog"><h1><?php echo $this->translation()->form_login ?></h1></div>
        <div id="ftitreg" style="display:none"><h1><?php echo $this->translation()->form_register ?></h1></div>
        <table>
        <tr>
        <th><?php echo $this->translation()->form_email ?>: </th>
        <td><input type="text" name="email"></td>
        </tr>
        <tr>
        <th><?php echo $this->translation()->form_password ?>: </th>
        <td><input type="password" name="password"></td>
        </tr>
        <tr id="confirmrow" style="display:none">
        <th><?php echo $this->translation()->form_confirm ?>: </th>
        <td><input type="password" name="confirm"></td>
        </tr>
        <tr id="lnkrow">
        <th>&nbsp;</th>
        <td>
            <a id="reglnk"><?php echo $this->translation()->form_not_registered ?></a> | 
            <a id="pwdlnk"><?php echo $this->translation()->form_forgot_password ?></a>
        </td>
        </tr>
        <tr>
        <th>&nbsp;</th>
        <td class="buttons">
            <input name="regcancel" type="button" value="<?php echo $this->translation()->form_cancel ?>" style="display:none">
            <input name="frmsubmit" type="button" value="<?php echo $this->translation()->form_submit ?>">
        </td>
        </tr>
        <tr id="message" style="display:none">
        <th>&nbsp;</th>
        <td class="message"></td>
        </tr>
        </table>
    </form>    
</div>
