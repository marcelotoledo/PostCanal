<?php if(is_object($this->profile)) : ?>

<input type="hidden" name="uid" value="<?php echo $this->profile->getUID() ?>">
<table>
    <tr>
        <td class="formtitle">
            <span class="formtitle">Mudança de Senha de Acesso</span>
        </td>
        <td class="formloading">
            <div id="spinner">&nbsp;</div>
        </td>
    </tr>
</table>
<table id="pwdform">
    <tr>
        <td class="formlabel">E-mail:</td>
        <td><?php echo $this->profile->login_email ?></td>
    </tr>
    <tr>
        <td class="formlabel">Senha:</td>
        <td><input type="password" name="password"></td>
    </tr>
    <tr>
        <td class="formlabel">Confirmar Senha:</td>
        <td><input type="password" name="confirm"></td>
    </tr>
    <tr class="formbutton">
        <td>&nbsp;</td>
        <td><input name="pwdchangesubmit" type="button" value="Alterar"></td>
    </tr>
</table>

<p id="changenotice" style="display:none">
Senha alterada com sucesso. 
<?php $this->DefaultHelper()->href("clique aqui") ?> 
para acessar a página de autenticação</p>

<?php else : ?>

<p>Este link expirou. Utilize a 
<?php $this->DefaultHelper()->href("página principal") ?> para solicitar um
novo pedido de recuperação de senha.</p>

<?php endif ?>
