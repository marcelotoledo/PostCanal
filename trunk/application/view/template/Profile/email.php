<?php if(is_object($this->profile) && strlen($this->new_email) > 0) : ?>

<h1>Mudança de E-mail de Acesso</h1>
<input type="hidden" name="email" value="<?php echo $this->profile->login_email ?>">
<input type="hidden" name="uid" value="<?php echo $this->profile->uid ?>">
<table id="emlform">
    <tr>
        <td class="formlabel">E-mail atual:</td>
        <td><?php echo $this->profile->login_email ?></td>
    </tr>
    <tr>
        <td class="formlabel">Novo E-mail:</td>
        <td><?php echo $this->new_email ?></td>
    </tr>
    <tr>
        <td class="formlabel">Senha:</td>
        <td><input type="password" name="password"></td>
    </tr>
    <tr class="formbutton">
        <td>&nbsp;</td>
        <td><input name="emlchangesubmit" type="button" value="Alterar"></td>
    </tr>
</table>

<p id="changenotice" style="display:none">
E-mail alterado com sucesso. <?php B_Helper::a("clique aqui") ?> 
para acessar a página de autenticação</p>

<?php else : ?>

<p>Este link expirou. É necessário solicitar um novo pedido de alteração de e-mail.</p>

<?php endif ?>
