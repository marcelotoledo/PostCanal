<br/>
<div id="subcontainer">
<form>
<h1><?php echo $this->translation()->blog_add ?></h1>
<table>

<tr id="discover_url_input_row">
<th><?php echo $this->translation()->blog_url ?>:</th>
<td>
    <input type="text" id="discover_url_input" value="">
    <a id="discover_url_lnk"><?php echo $this->translation()->blog_discover_url ?></a>
</td>
</tr>
<tr id="discover_url_result_row" style="display:none">
<th><?php echo $this->translation()->blog_url ?>:</th>
<td>
    <div id="discover_url_display"></div>
    <a id="discover_url_change_lnk"><?php echo $this->translation()->blog_change_url ?></a>
</td>
</tr>

<tr id="blog_type_row" style="display:none">
<th><?php echo $this->translation()->blog_type ?>:</th>
<td><div id="blog_type_display"></div></td>
</tr>

<tr id="manager_url_input_row" style="display:none">
<th><?php echo $this->translation()->blog_manager_url ?>:</th>
<td>
    <input type="text" id="manager_url_input" value="">
    <a id="manager_url_check_lnk"><?php echo $this->translation()->blog_manager_url_check ?></a>
</td>
</tr>
<tr id="manager_url_result_row" style="display:none">
<th><?php echo $this->translation()->blog_manager_url ?>:</th>
<td>
    <div id="manager_url_display"></div>&nbsp;
    <a id="manager_url_change_lnk"><?php echo $this->translation()->blog_manager_url_change ?></a>
</td>
</tr>

</table>

<table id="login_table" style="display:none">
<tr>
<th><?php echo $this->translation()->blog_username ?>:</th>
<td><input type="text" id="username_input"></td>
</tr>
<tr>
<th><?php echo $this->translation()->blog_password ?>:</th>
<td>
    <input type="password" id="password_input">
    <!--
    <a id="login_check_lnk"><?php echo $this->translation()->blog_login_check ?></a>
    -->
</td>
</tr>
</table>

<table id="blog_buttons_table" style="display:none">
<tr>
<th>&nbsp;</th>
<td class="buttons">
    <input id="add_submit_button" type="button" value="<?php echo $this->translation()->submit ?>">
</td>
</tr>
</table>

<table>
<tr id="blog_add_message" style="display:none">
<th>&nbsp;</th>
<td class="message"></td>
</tr>
</table>

<table id="blog_types_info" style="display:none">
<tr>
<th>&nbsp;</th>
<td>
    <table>
        <tr>
            <td><b>Type</b></td>
            <td><b>Example of URL</b></td>
        </tr>
        <tr>
            <td><u>WordPress</u></td>
            <td><i>http://mylogin.wordpress.com</i><br/>
                <i>http://mywordpressdomain.com</i></td>
        </tr>
    </table>
</td>
</tr>
</table>

</form>
</div>
