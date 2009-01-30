<?php if ($this->result == ProfileController::CONFIRM_OK) : ?>
<p>O cadastro de seu perfil foi confirmado com sucesso!</p>
<p>O próximo passo é acessar a página principal e utilizar seu e-mail e senha
cadastrados:</p>
<p><?php $this->DefaultHelper()->a("página principal", null) ?></p>

<?php elseif ($this->result == ProfileController::CONFIRM_DONE_BEFORE) : ?>
<p>O cadastro de seu perfil já foi confirmado anteriormente.</p>
<p>Para ter acesso ao sistema, basta acessar a página principal e utilizar seu
e-mail e senha já cadastrados:</p>
<p><?php $this->DefaultHelper()->a("página principal", null) ?></p>

<?php elseif ($this->result == ProfileController::CONFIRM_FAILED) : ?>
<p>Perfil inválido.</p>
<?php endif ?>