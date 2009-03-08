<h1>Editar perfil</h1>
<form id="editform">
<table>
    <tr>
        <td class="formlabel">Nome:</td>
        <td><input type="text" name="name" 
            value="<?php echo $this->profile->name ?>"></td>
    </tr>
    <tr class="formbutton">
        <td style="text-align:right">
            <div id="spinner" style="float:right">&nbsp;</div>
        </td>
        <td>
        <input name="editcancel" type="reset" value="Cancelar">
        <input name="editsubmit" type="button" value="Alterar">
        </td>
    </tr>
</table>
</form>
<br>
<form id="pwdchangeform">
<table>
    <tr>
        <td class="formlabel">&nbsp;</td>
        <td><a id="pwdchangelnk">alterar senha</a></td>
    </tr>
    <tr>
        <td class="formlabel">Senha atual:</td>
        <td><input type="password" name="current_password" disabled></td>
    </tr>
    <tr>
        <td class="formlabel">Nova senha:</td>
        <td><input type="password" name="new_password" disabled></td>
    </tr>
    <tr>
        <td class="formlabel">Confirmar senha:</td>
        <td><input type="password" name="confirm_password" disabled></td>
    </tr>
    <tr class="formbutton">
        <td style="text-align:right">
            <div id="spinner" style="float:right">&nbsp;</div>
        </td>
        <td>
        <input name="pwdchangecancel" type="button" value="Cancelar" disabled>
        <input name="pwdchangesubmit" type="button" value="Alterar" disabled>
        </td>
    </tr>
</table>
</form>
<br>
<form id="emlchangeform">
<table>
    <tr>
        <td class="formlabel">&nbsp;</td>
        <td><a id="emlchangelnk">alterar e-mail</a></td>
    </tr>        
    <tr>
        <td class="formlabel">E-mail:</td>
        <td><input type="login_email" name="login_email"
            value="<?php echo $this->profile->login_email ?>" disabled></td>
    </tr>
    <tr class="formbutton">
        <td style="text-align:right">
            <div id="spinner" style="float:right">&nbsp;</div>
        </td>
        <td>
        <input name="emlchangecancel" type="reset" value="Cancelar" disabled>
        <input name="emlchangesubmit" type="button" value="Alterar" disabled>
        </td>
    </tr>
</table>
</form>
