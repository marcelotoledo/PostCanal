<form id="editform">
    <h1><?php echo $this->translation->edit_profile ?></h1>
    <table>
        <tr>
        <th><?php echo $this->translation->profile_name ?>:</th>
        <td><input type="text" name="name" value="<?php echo $this->profile->name ?>"></td>
        </tr>
        <tr>
        <th>&nbsp;</th>
        <td class="buttons">
            <input name="editcancel" type="reset" value="<?php echo $this->translation->application_cancel ?>">
            <input name="editsubmit" type="button" value="<?php echo $this->translation->application_update ?>">
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
        <td><a id="pwdchangelnk"><?php echo $this->translation->update_password ?></a></td>
        </tr>
        <tr>
        <th><?php echo $this->translation->current_password ?>:</th>
        <td><input type="password" name="current_password" disabled></td>
        </tr>
        <tr>
        <th><?php echo $this->translation->new_password ?>:</th>
        <td><input type="password" name="new_password" disabled></td>
        </tr>
        <tr>
        <th><?php echo $this->translation->password_confirm ?>:</th>
        <td><input type="password" name="confirm_password" disabled></td>
        </tr>
        <tr>
        <th>&nbsp;</th>
        <td class="buttons">
            <input name="pwdchangecancel" type="button" value="<?php echo $this->translation->application_cancel ?>" disabled>
            <input name="pwdchangesubmit" type="button" value="<?php echo $this->translation->application_update ?>" disabled>
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
        <td><a id="emlchangelnk"><?php echo $this->translation->update_email ?></a></td>
        </tr>
        <tr>
        <th><?php echo $this->translation->application_email ?>:</th>
        <td><input type="text" name="login_email" value="<?php echo $this->profile->login_email ?>" disabled></td>
        </tr>
        <tr>
        <th>&nbsp;</th>
        <td class="buttons">
            <input name="emlchangecancel" type="button" value="<?php echo $this->translation->application_cancel ?>" disabled>
            <input name="emlchangesubmit" type="button" value="<?php echo $this->translation->application_update ?>" disabled>
        </td>
        </tr>
        </tr>
        <tr id="emlchangemessage" style="display:none">
        <th>&nbsp;</th>
        <td class="message"></td>
        </tr>
    </table>
</form>
