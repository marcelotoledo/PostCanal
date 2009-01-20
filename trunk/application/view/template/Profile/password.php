<?php if($this->response = ProfileController::PASSWORD_SHOW_FORM &&
         is_object($this->profile)) : ?>
<!-- { password change form -->
<input type="hidden" name="uid" value="<?php echo $this->profile->getUID() ?>">
<table>
    <tr id="pwdchangetitlerow">
        <td colspan="2" class="formtitle">
            <span class="formtitle">Mudança de Senha de Acesso</span>
        </td>
    </tr>
    <tr>
        <td class="fieldlabel">E-mail:</td>
        <td><?php echo $this->profile->login_email ?></td>
    </tr>
    <tr>
        <td class="fieldlabel">Senha:</td>
        <td><input type="password" name="password"></td>
    </tr>
    <tr id="pwdconfrow">
        <td class="fieldlabel">Confirmar Senha:</td>
        <td><input type="password" name="confirm"></td>
    </tr>
    <tr style="height:50px;vertical-align:bottom">
        <td>&nbsp;</td>
        <td style="text-align:right"><input name="pwdchangesubmit" type="button" value="Alterar"></td>
    </tr>
</table>
<!-- password change form } -->
<?php else : ?>
<p>Perfil inválido.</p>
<?php endif ?>
