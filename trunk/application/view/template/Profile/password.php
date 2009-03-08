<?php if(is_object($this->profile)) : ?>

<h1>Mudança de Senha de Acesso</h1>
<input type="hidden" name="email" value="<?php echo $this->profile->login_email ?>">
<input type="hidden" name="uid" value="<?php echo $this->profile->uid ?>">
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

<p id="changenotice" style="display:none">Senha alterada com sucesso. <?php B_Helper::a("clique aqui") ?> para acessar a página de autenticação</p>

<?php else : ?>

<p>Este link expirou. Utilize a <?php B_Helper::a("página principal") ?> para solicitar um pedido de recuperação de senha.</p>

<?php endif ?>
