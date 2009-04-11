<form>
    <h1><?php echo $this->translation()->blog_add ?></h1>
    <table>
    <tr>
    <th><?php echo $this->translation()->blog_name ?>:</th>
    <td><input type="text" name="blog_name" value="<?php echo $this->blog->name ?>"></td>
    </tr>
    <tr>
    <th><?php echo $this->translation()->blog_url ?>:</th>
    <td>
        <input type="text" name="discover_url" value="">
        <div id="discover_url_display" style="display:none"></div>
        <a id="discover_url"><?php echo $this->translation()->blog_discover_url ?></a>
        <a id="change_url" style="display:none"><?php echo $this->translation()->blog_change_url ?></a>
    </td>
    </tr>
    <tr id="blog_type_row" style="display:none">
    <th><?php echo $this->translation()->blog_type ?>:</th>
    <td><div id="blog_type_display"></div></td>
    </tr>
    <tr id="manager_url_row" style="display:none">
    <th><?php echo $this->translation()->blog_manager_url ?>:</th>
    <td>
        <input type="text" name="manager_url" value="">
        <a id="check_manager_url"><?php echo $this->translation()->blog_manager_url_check ?></a>
        <div id="manager_url_display" style="display:none"></div>
    </td>
    </tr>
    </table>
    <table id="manager_login_row" style="display:none">
    <tr>
    <th><?php echo $this->translation()->blog_username ?>:</th>
    <td><input type="text" name="manager_username"></td>
    </tr>
    <tr>
    <th><?php echo $this->translation()->blog_password ?>:</th>
    <td>
        <input type="password" name="manager_password">
        <a id="check_manager_login"><?php echo $this->translation()->blog_login_check ?></a>
    </td>
    </tr>
    </table>
    <table>
    <tr>
    <th>&nbsp;</th>
    <td class="buttons">
        <input name="addsubmit" type="button" value="<?php echo $this->translation()->application_add ?>">
    </td>
    </tr>
    <tr id="blogaddmessage" style="display:none">
    <th>&nbsp;</th>
    <td class="message"></td>
    </tr>
    </table>
</form>
