<?php if(is_object($this->profile)) : ?>

<div id="pwdform">
<form>
    <h1>Mudança de Senha de Acesso</h1>
    <input type="hidden" name="email" value="<?php echo $this->profile->login_email ?>">
    <input type="hidden" name="uid" value="<?php echo $this->profile->uid ?>">
    <dl>
        <dt>E-mail:</dt>
        <dd><i><?php echo $this->profile->login_email ?></i></dd>
        <dt>Senha:</dt>
        <dd><input type="password" name="password"></dd>
        <dt>Confirmar Senha:</dt>
        <dd><input type="password" name="confirm"></dd>
        <dt>&nbsp;</dt>
        <dd class="buttons">
            <input name="pwdchangesubmit" type="button" value="Alterar">
        </dd>
        <div id="message" style="display:none">
        <dt>&nbsp;</dt>
        <dd class="message"></dd>
        </div>
    </dl>
</form>
</div>

<p id="changenotice" style="display:none">Senha alterada com sucesso. <?php B_Helper::a("clique aqui") ?> para acessar a página de autenticação</p>

<?php else : ?>

<p>Este link expirou. Utilize a <?php B_Helper::a("página principal") ?> para solicitar um pedido de recuperação de senha.</p>

<?php endif ?>
