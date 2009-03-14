<form id="editform">
    <h1>Editar perfil</h1>
    <table>
        <tr>
        <th>Nome:</th>
        <td><input type="text" name="name" value="<?php echo $this->profile->name ?>"></td>
        </tr>
        <tr>
        <th>&nbsp;</th>
        <td class="buttons">
            <input name="editcancel" type="reset" value="Cancelar">
            <input name="editsubmit" type="button" value="Alterar">
        </td>
        </tr>
        <tr id="editmsg" style="display:none">
        <th>&nbsp;</th>
        <td class="message"></td>
        </tr>
    </table>
</form>
<br>
<form id="pwdchangeform">
    <table>
        <tr>
        <th>&nbsp;</th>
        <td><a id="pwdchangelnk">alterar senha</a></td>
        </tr>
        <tr>
        <th>Senha atual:</th>
        <td><input type="password" name="current_password" disabled></td>
        </tr>
        <tr>
        <th>Nova senha:</th>
        <td><input type="password" name="new_password" disabled></td>
        </tr>
        <tr>
        <th>Confirmar senha:</th>
        <td><input type="password" name="confirm_password" disabled></td>
        </tr>
        <tr>
        <th>&nbsp;</th>
        <td class="buttons">
            <input name="pwdchangecancel" type="button" value="Cancelar" disabled>
            <input name="pwdchangesubmit" type="button" value="Alterar" disabled>
        </td>
        </tr>
        <tr id="pwdchangemsg" style="display:none">
        <th>&nbsp;</th>
        <td class="message"></td>
        </tr>
    </table>
</form>
<br>
<form id="emlchangeform">
    <table>
        <tr>
        <th>&nbsp;</th>
        <td><a id="emlchangelnk">alterar e-mail</a></td>
        </tr>
        <tr>
        <th>E-mail:</th>
        <td><input type="text" name="login_email" value="<?php echo $this->profile->login_email ?>" disabled></td>
        </tr>
        <tr>
        <th>&nbsp;</th>
        <td class="buttons">
            <input name="emlchangecancel" type="button" value="Cancelar" disabled>
            <input name="emlchangesubmit" type="button" value="Alterar" disabled>
        </td>
        </tr>
        <tr id="emlchangemsg" style="display:none">
        <th>&nbsp;</th>
        <td class="message"></td>
        </tr>
    </table>
</form>
