<div id="logo">
    <h1>POSTCANAL</h1>
</div>
<div id="form">
    <form>
        <div id="ftitlog"><h1><?php echo $this->translation()->login ?></h1></div>
        <div id="ftitreg" style="display:none"><h1><?php echo $this->translation()->register ?></h1></div>
        <table>
        <tr>
        <th><?php echo $this->translation()->email ?>: </th>
        <td><input type="text" id="email"></td>
        </tr>
        <tr>
        <th><?php echo $this->translation()->password ?>: </th>
        <td><input type="password" id="password"></td>
        </tr>
        <tr id="confirmrow" style="display:none">
        <th><?php echo $this->translation()->confirm_password ?>: </th>
        <td><input type="password" id="passwordc"></td>
        </tr>
        <tr id="lnkrow">
        <th>&nbsp;</th>
        <td>
            <a href="#" id="reglnk"><?php echo $this->translation()->not_registered ?></a> | 
            <a href="#" id="pwdlnk"><?php echo $this->translation()->forgot_password ?></a>
        </td>
        </tr>
        <tr>
        <th>&nbsp;</th>
        <td class="buttons">
            <input id="regcancel" type="button" value="<?php echo $this->translation()->cancel ?>" style="display:none">
            <input id="frmsubmit" type="button" value="<?php echo $this->translation()->submit ?>">
        </td>
        </tr>
        <tr id="message" style="display:none">
        <th>&nbsp;</th>
        <td class="message"></td>
        </tr>
        </table>
    </form>    
</div>
<div style="clear:both"></div>
