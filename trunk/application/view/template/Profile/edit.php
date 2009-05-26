<br/>
<div id="subcontainer">
<form id="editform">
    <h1><?php echo $this->translation()->profile ?></h1>
    <table>
        <tr>
        <th><?php echo $this->translation()->name ?>:</th>
        <td><input type="text" id="name" value="<?php echo $this->profile->name ?>"></td>
        </tr>
        <tr>
        <th>&nbsp;</th>
        <td class="buttons">
            <input id="editsubmit" type="button" value="<?php echo $this->translation()->submit ?>">
        </td>
        </tr>
        <tr id="editmessage" style="display:none">
        <th>&nbsp;</th>
        <td class="message"></td>
        </tr>
    </table>
</form>
<br>
<form id="pwdchangeform">
    <table>
        <tr>
        <th>&nbsp;</th>
        <td><a href="#" id="pwdchangelnk"><?php echo $this->translation()->update_password ?></a></td>
        </tr>
        <tr>
        <th><?php echo $this->translation()->current_password ?>:</th>
        <td><input type="password" id="currentpwd" disabled></td>
        </tr>
        <tr>
        <th><?php echo $this->translation()->new_password ?>:</th>
        <td><input type="password" id="newpwd" disabled></td>
        </tr>
        <tr>
        <th><?php echo $this->translation()->confirm_password ?>:</th>
        <td><input type="password" id="confirmpwd" disabled></td>
        </tr>
        <tr>
        <th>&nbsp;</th>
        <td class="buttons">
            <input id="pwdchangecancel" type="button" value="<?php echo $this->translation()->cancel ?>" disabled>
            <input id="pwdchangesubmit" type="button" value="<?php echo $this->translation()->submit ?>" disabled>
        </td>
        </tr>
        </tr>
        <tr id="pwdchangemessage" style="display:none">
        <th>&nbsp;</th>
        <td class="message"></td>
        </tr>
    </table>
</form>
<br>
<form id="emlchangeform">
    <table>
        <tr>
        <th>&nbsp;</th>
        <td><a href="#" id="emlchangelnk"><?php echo $this->translation()->update_email ?></a></td>
        </tr>
        <tr>
        <th><?php echo $this->translation()->email ?>:</th>
        <td><input type="text" id="neweml" value="<?php echo $this->profile->login_email ?>" disabled></td>
        </tr>
        <tr>
        <th>&nbsp;</th>
        <td class="buttons">
            <input id="emlchangecancel" type="button" value="<?php echo $this->translation()->cancel ?>" disabled>
            <input id="emlchangesubmit" type="button" value="<?php echo $this->translation()->submit ?>" disabled>
        </td>
        </tr>
        </tr>
        <tr id="emlchangemessage" style="display:none">
        <th>&nbsp;</th>
        <td class="message"></td>
        </tr>
    </table>
</form>
</div>
