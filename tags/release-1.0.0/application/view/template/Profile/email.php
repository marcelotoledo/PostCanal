<?php if(is_object($this->profile) && strlen($this->new_email) > 0) : ?>

<form id="emlform">
    <h1><?php echo $this->translation()->change_email ?></h1>
    <input type="hidden" id="email" value="<?php echo $this->profile->login_email ?>">
    <input type="hidden" id="user" value="<?php echo $this->profile->hash ?>">
    <table>
    <tr>
        <th><?php echo $this->translation()->current_email ?>: </th>
        <td><i><?php echo $this->profile->login_email ?></i></td>
    </tr>
    <tr>
        <th><?php echo $this->translation()->new_email ?>: </th>
        <td><i><?php echo $this->new_email ?></i></td>
    </tr>
    <tr>
        <th><?php echo $this->translation()->password ?>:</th>
        <td><input type="password" id="password"></td>
    </tr>
    <tr>
        <th>&nbsp;</th>
        <td class="buttons">
            <input id="emlchangesubmit" type="button" value="<?php echo $this->translation()->submit ?>">
        </td>
    </tr>
    <tr id="message" style="display:none">
    <th>&nbsp;</th>
    <td class="message"></td>
    </tr>
</table>
</form>

<p id="changenotice" style="display:none">
<?php echo $this->translation()->email_change_msg_1 ?>. <a href="/"><?php echo $this->translation()->click_here ?></a> <?php echo $this->translation()->email_change_msg_2 ?></p>

<?php else : ?>

<p><?php echo $this->translation()->link_expired_1 ?> <a href="/"><?php echo $this->translation()->main_page ?></a> <?php echo $this->translation()->link_expired_2 ?>.</p>

<?php endif ?>
