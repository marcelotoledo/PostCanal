<?php if(is_object($this->profile)) : ?>

<div id="pwdform">
<form>
    <h1>Mudança de Senha de Acesso</h1>
    <input type="hidden" name="email" value="<?php echo $this->profile->login_email ?>">
    <input type="hidden" name="user" value="<?php echo $this->profile->hash ?>">
    <table>
        <tr>
        <th>E-mail:</th>
        <td><i><?php echo $this->profile->login_email ?></i></td>
        </tr>
        <tr>
        <th>Senha:</th>
        <td><input type="password" name="password"></td>
        </tr>
        <tr>
        <th>Confirmar Senha:</th>
        <td><input type="password" name="confirm"></td>
        </tr>
        <tr>
        <th>&nbsp;</th>
        <td class="buttons">
            <input name="pwdchangesubmit" type="button" value="Alterar">
        </td>
        </tr>
        <tr id="message" style="display:none">
        <th>&nbsp;</th>
        <td class="message"></td>
        </tr>
    </table>
</form>
</div>

<p id="changenotice" style="display:none">Senha alterada com sucesso. <?php B_Helper::a("clique aqui") ?> para acessar a página de autenticação</p>

<?php else : ?>

<p>Este link expirou. Utilize a <?php B_Helper::a("página principal") ?> para solicitar um pedido de recuperação de senha.</p>

<?php endif ?>
