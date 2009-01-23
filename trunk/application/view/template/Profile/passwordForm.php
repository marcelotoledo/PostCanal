<?php if(is_object($this->profile)) : ?>

<input type="hidden" name="uid" value="<?php echo $this->profile->getUID() ?>">
<table id="container">
    <tr>
        <td class="formtitle">
            <span class="formtitle">Mudança de Senha de Acesso</span>
        </td>
        <td class="formloading">
            <div id="spinner">&nbsp;</div>
        </td>
    </tr>
    <tr>
        <td class="formlabel">E-mail:</td>
        <td><?php echo $this->profile->login_email ?></td>
    </tr>
    <tr>
        <td class="formlabel">Senha:</td>
        <td><input type="password" name="password"></td>
    </tr>
    <tr id="pwdconfrow">
        <td class="formlabel">Confirmar Senha:</td>
        <td><input type="password" name="confirm"></td>
    </tr>
    <tr class="formbutton">
        <td>&nbsp;</td>
        <td><input name="pwdchangesubmit" type="button" value="Alterar"></td>
    </tr>
</table>

<?php else : ?>

<p>Perfil inválido.</p>

<?php endif ?>
