<form>
    <h1>Adicionar CMS</h1>
    <dl>
    <dt>Nome:</dt>
    <dd><input type="text" name="name" value="<?php echo $this->cms->name ?>"></dd>
    <dt>Endereço de CMS:</dt>
    <dd>
        <input type="text" name="input_url" value="">
        <div id="input_url_ro" style="display:none"></div>
        <a id="check_url">verificar</a>
        <a id="change_url" style="display:none">modificar</a>
    </dd>
    <div id="cms_type_row" style="display:none">
    <dt>Tipo de CMS:</dt>
    <dd><div id="input_cms_type_ro"></div></dd>
    </div>
    <div id="manager_url_row" style="display:none">
    <dt>Gerenciador:</dt>
    <dd>
        <input type="text" name="manager_url" value="">
        <a id="check_manager_url">verificar</a>
        <div id="input_manager_url_ro" style="display:none"></div>
    </dd>
    </div>
    <div id="manager_login_row" style="display:none">
    <dt>Usuário:</dt>
    <dd><input type="text" name="manager_username"></dd>
    <dt>Senha:</dt>
    <dd>
        <input type="password" name="manager_password">
        <a id="check_manager_login">verificar</a>
    </dd>
    </div>

    <dt>&nbsp;</dt>
    <dd class="buttons">
        <input name="addsubmit" type="button" value="Adicionar">
    </dd>
    <div id="message" style="display:none">
    <dt>&nbsp;</dt>
    <dd class="message"></dd>
    </div>

    </dl>
</form>
