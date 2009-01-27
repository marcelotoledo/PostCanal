<table>
    <tr>
        <td class="formtitle">
            <span id="authtitle">Editar perfil</span>
        </td>
        <td class="formloading">
            <div id="spinner">&nbsp;</div>
        </td>
    </tr>
    <tr>
        <td class="formlabel">Nome:</td>
        <td><input type="text" name="name" 
            value="<?php echo $this->information->name ?>"></td>
    </tr>
</table>
<br>
<table>
    <tr>
        <td class="formlabel">&nbsp;</td>
        <td><a id="mailchangelnk">trocar e-mail</a></td>
    </tr>        
    <tr>
        <td class="formlabel">E-mail:</td>
        <td><input type="login_email" name="login_email"
            value="<?php echo $this->profile->login_email ?>" disabled></td>
    </tr>
</table>
<br>
<input type="hidden" name="pwdchange" value="no">
<table>
    <tr>
        <td class="formlabel">&nbsp;</td>
        <td><a id="pwdchangelnk">trocar senha</a></td>
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
        <td><input type="password" name="new_password_confirm" disabled></td>
    </tr>
    <tr class="formbutton">
        <td>&nbsp;</td>
        <td>
        <input name="editcancel" type="button" value="Cancelar">
        <input name="editsubmit" type="button" value="Alterar">
        </td>
    </tr>
</table>
