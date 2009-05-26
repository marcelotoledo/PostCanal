<?php if(is_object($this->profile)) : ?>

<div id="pwdform">
<form>
    <h1><?php echo $this->translation()->password_change ?></h1>
    <input type="hidden" id="email" value="<?php echo $this->profile->login_email ?>">
    <input type="hidden" id="user" value="<?php echo $this->profile->hash ?>">
    <table>
        <tr>
        <th><?php echo $this->translation()->email ?>: </th>
        <td><i><?php echo $this->profile->login_email ?></i></td>
        </tr>
        <tr>
        <th><?php echo $this->translation()->password ?>: </th>
        <td><input type="password" name="password"></td>
        </tr>
        <tr>
        <th><?php echo $this->translation()->confirm_password ?>: </th>
        <td><input type="password" name="passwordc"></td>
        </tr>
        <tr>
        <th>&nbsp;</th>
        <td class="buttons">
            <input id="pwdchangesubmit" type="button" value="<?php echo $this->translation()->change ?>">
        </td>
        </tr>
        <tr id="message" style="display:none">
        <th>&nbsp;</th>
        <td class="message"></td>
        </tr>
    </table>
</form>
</div>

<p id="changenotice" style="display:none"><?php echo $this->translation()->password_change_msg_1 ?>. <?php B_Helper::a($this->translation()->click_here) ?> <?php echo $this->translation()->password_change_msg_2 ?></p>

<?php else : ?>

<p><?php echo $this->translation()->link_expired_1 ?> <?php B_Helper::a($this->translation()->main_page) ?> <?php echo $this->translation()->link_expired_2 ?>.</p>

<?php endif ?>
