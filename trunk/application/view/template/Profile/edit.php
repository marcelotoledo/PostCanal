<form id="editform">
    <h1>Editar perfil</h1>
    <dl>
        <dt>Nome:</dt>
        <dd><input type="text" name="name" value="<?php echo $this->profile->name ?>"></dd>
        <dt>&nbsp;</dt>
        <dd class="buttons">
            <input name="editcancel" type="reset" value="Cancelar">
            <input name="editsubmit" type="button" value="Alterar">
        </dd>
        <div id="editmsg" style="display:none">
        <dt>&nbsp;</dt>
        <dd class="message"></dd>
        </div>
    </dl>
</form>

<div style="clear:both;height:10px;">&nbsp;</div>

<form id="pwdchangeform">
    <dl>
        <dt>&nbsp;</dt>
        <dd><a id="pwdchangelnk">alterar senha</a></dd>
        <dt>Senha atual:</dt>
        <dd><input type="password" name="current_password" disabled></dd>
        <dt>Nova senha:</dt>
        <dd><input type="password" name="new_password" disabled></dd>
        <dt>Confirmar senha:</dt>
        <dd><input type="password" name="confirm_password" disabled></dd>
        <dt>&nbsp;</dt>
        <dd class="buttons">
            <input name="pwdchangecancel" type="button" value="Cancelar" disabled>
            <input name="pwdchangesubmit" type="button" value="Alterar" disabled>
        </dd>
        <div id="pwdchangemsg" style="display:none">
        <dt>&nbsp;</dt>
        <dd class="message"></dd>
        </div>
    </dl>
</form>

<div style="clear:both;height:10px;">&nbsp;</div>

<form id="emlchangeform">
    <dl>
        <dt>&nbsp;</dt>
        <dd><a id="emlchangelnk">alterar e-mail</a></dd>
        <dt>E-mail:</dt>
        <dd><input type="text" name="login_email" value="<?php echo $this->profile->login_email ?>" disabled></dd>
        <dt>&nbsp;</dt>
        <dd class="buttons">
            <input name="emlchangecancel" type="button" value="Cancelar" disabled>
            <input name="emlchangesubmit" type="button" value="Alterar" disabled>
        </dd>
        <div id="emlchangemsg" style="display:none">
        <dt>&nbsp;</dt>
        <dd class="message"></dd>
        </div>
    </dl>
</form>
