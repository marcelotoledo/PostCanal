<form>
    <h1>Adicionar Blog</h1>
    <table>
    <tr>
    <th>Nome:</th>
    <td><input type="text" name="name" value="<?php echo $this->blog->name ?>"></td>
    </tr>
    <tr>
    <th>Endereço de Blog:</th>
    <td>
        <input type="text" name="input_url" value="">
        <div id="input_url_ro" style="display:none"></div>
        <a id="check_url">verificar</a>
        <a id="change_url" style="display:none">modificar</a>
    </td>
    </tr>
    <tr id="blog_type_row" style="display:none">
    <th>Tipo de Blog:</th>
    <td><div id="input_blog_type_ro"></div></td>
    </tr>
    <tr id="manager_url_row" style="display:none">
    <th>Gerenciador:</th>
    <td>
        <input type="text" name="manager_url" value="">
        <a id="check_manager_url">verificar</a>
        <div id="input_manager_url_ro" style="display:none"></div>
    </td>
    </tr>
    </table>
    <table id="manager_login_row" style="display:none">
    <tr>
    <th>Usuário:</th>
    <td><input type="text" name="manager_username"></td>
    </tr>
    <tr>
    <th>Senha:</th>
    <td>
        <input type="password" name="manager_password">
        <a id="check_manager_login">verificar</a>
    </td>
    </tr>
    </table>
    <table>
    <tr>
    <th>&nbsp;</th>
    <td class="buttons">
        <input name="addsubmit" type="button" value="Adicionar">
    </td>
    </tr>
    </table>
</form>