<?php if ($this->response == ProfileController::CONFIRM_OK) : ?>
<p>O cadastro de seu perfil foi confirmado com sucesso!</p>
<p>O próximo passo é acessar a página principal e utilizar seu e-mail e senha
cadastrados:</p>
<p><a href="<?php echo BASE_URL ?>"><?php echo BASE_URL ?></a></p>

<?php elseif ($this->response == ProfileController::CONFIRM_DONE_BEFORE) : ?>
<p>O cadastro de seu perfil já foi confirmado anteriormente.</p>
<p>Para ter acesso ao sistema, basta acessar a página principal e utilizar seu
e-mail e senha já cadastrados:</p>
<p><a href="<?php echo BASE_URL ?>"><?php echo BASE_URL ?></a></p>

<?php elseif ($this->response == ProfileController::CONFIRM_FAILED) : ?>
<p>Perfil inválido.</p>
<?php endif ?>
