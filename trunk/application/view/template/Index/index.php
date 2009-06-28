<div id="logo">
    <span>POSTCANAL</span>
</div>

<div class="rightform" id="loginform">
    <h1><?php echo $this->translation()->sign_in ?></h1>
    <div class="formcontainer">
    <form>
        <div class="inputcontainer">
            <div class="inputlabel"><?php echo $this->translation()->email ?></div>
            <input type="text" name="email" size="20">
        </div>
        <div class="inputcontainer">
            <div class="inputlabel"><?php echo $this->translation()->password ?></div>
            <input type="text" name="password" size="20">
        </div>
        <div class="inputlinks">
            <a href="#" id="pwdlnk"><?php echo $this->translation()->forgot_password ?>?</a>
        </div>
        <div class="inputbuttons">
            <input id="loginsubmit" type="button" value="<?php echo $this->translation()->submit ?>">
        </div>
        <div class="inputmessage">
        </div>
    </form>
    </div>
    <div class="bottomlnk">
        <a href="./profile/signup"><?php echo $this->translation()->not_a_member ?>?</a>
    </div>
</div>

<div class="rightform" id="recoveryform" style="display:none">
    <h1><?php echo $this->translation()->forgot_password ?>?</h1>
    <div class="formcontainer">
    <form>
        <div class="inputcontainer">
            <div class="inputlabel"><?php echo $this->translation()->email ?></div>
            <input type="text" name="email" size="20">
        </div>
        <div class="inputbuttons">
            <input id="recoverysubmit" type="button" value="<?php echo $this->translation()->retrieve ?>">
        </div>
        <div class="inputmessage">
        </div>
    </form>
    </div>
    <div class="bottomlnk">
        <a href="#" id="siglnk"><?php echo $this->translation()->remembered_let_me_sign_in ?></a>
    </div>
</div>

<!--
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
-->

<div style="clear:both"></div>
