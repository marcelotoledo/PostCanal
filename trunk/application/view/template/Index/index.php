<div id="logo">
    <h1>BLOTŎMATE</h1>
</div>
<div id="form">
    <form>
        <div id="ftitlog"><h1><?php echo $this->translation->form_login ?></h1></div>
        <div id="ftitreg" style="display:none"><h1><?php echo $this->translation->form_register ?></h1></div>
        <dl>
        <dt><?php echo $this->translation->form_email ?></dt>
        <dd><input type="text" name="email"></dd>
        <dt><?php echo $this->translation->form_password ?></dt>
        <dd><input type="password" name="password"></dd>
        <div id="confirmrow" style="display:none">
        <dt><?php echo $this->translation->form_confirm ?></dt>
        <dd><input type="password" name="confirm"></dd>
        </div>
        <div id="lnkrow">
        <dt>&nbsp;</dt>
        <dd>
            <a id="reglnk"><?php echo $this->translation->form_not_registered ?></a> | 
            <a id="pwdlnk"><?php echo $this->translation->form_forgot_password ?></a>
        </dd>
        </div>
        <dt>&nbsp;</dt>
        <dd class="buttons">
            <input name="regcancel" type="button" value="<?php echo $this->translation->form_cancel ?>" style="display:none">
            <input name="frmsubmit" type="button" value="<?php echo $this->translation->form_submit ?>">
        </dd>
        <div id="message" style="display:none">
        <dt>&nbsp;</dt>
        <dd class="message"></dd>
        </div>
        </dl>
    </form>    
</div>
