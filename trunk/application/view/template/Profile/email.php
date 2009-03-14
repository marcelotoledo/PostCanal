<?php if(is_object($this->profile) && strlen($this->new_email) > 0) : ?>

<form>
    <h1>Mudança de E-mail de Acesso</h1>
    <input type="hidden" name="email" value="<?php echo $this->profile->login_email ?>">
    <input type="hidden" name="uid" value="<?php echo $this->profile->uid ?>">
    <table>
    <tr>
        <th>E-mail atual:</th>
        <td><i><?php echo $this->profile->login_email ?></i></td>
    </tr>
    <tr>
        <th>Novo E-mail:</th>
        <td><i><?php echo $this->new_email ?></i></td>
    </tr>
    <tr>
        <th>Senha:</th>
        <td><input type="password" name="password"></td>
    </tr>
    <tr>
        <th>&nbsp;</th>
        <td class="buttons">
            <input name="emlchangesubmit" type="button" value="Alterar">
        </td>
    </tr>
</table>
</form>

<p id="changenotice" style="display:none">
E-mail alterado com sucesso. <?php B_Helper::a("clique aqui") ?> 
para acessar a página de autenticação</p>

<?php else : ?>

<p>Este link expirou. É necessário solicitar um novo pedido de alteração de e-mail.</p>

<?php endif ?>
