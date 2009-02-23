<h1>Adicionar CMS</h1>
<form id="addform">
<table>
    <tr>
        <td class="formlabel">Nome:</td>
        <td><input type="text" name="name" 
            value="<?php echo $this->cms->name ?>"></td>
    </tr>
</table>
<br>
<table>
    <tr>
        <td class="formlabel">Endereço do CMS:</td>
        <td>
            <input type="text" name="input_url" value="">
            <div id="input_url_ro" style="display:none"></div>
            <a id="check_url">verificar</a>
            <a id="change_url" style="display:none">modificar</a>
        </td>
    </tr>
    <tr id="cms_type_row" style="display:none">
        <td class="formlabel">Tipo de CMS:</td>
        <td><div id="input_cms_type_ro"></div></td>
    </tr>
    <tr id="manager_url_row" style="display:none">
        <td class="formlabel">Gerenciador:</td>
        <td>
            <input type="text" name="manager_url" value="">
            <a id="check_manager_url">verificar</a>
            <div id="input_manager_url_ro" style="display:none"></div>
        </td>
    </tr>
</table>
<br>
<table>
    <tr>
        <td class="formlabel">Usuário:</td>
        <td><input type="text" name="manager_username"></td>
    </tr>
    <tr>
        <td class="formlabel">Senha:</td>
        <td>
            <input type="password" name="manager_password">
            <a id="check_manager_login" style="display:none">verificar</a>
        </td>
    </tr>
    <tr class="formbutton">
        <td>&nbsp;</td>
        <td>
        <input name="addcancel" type="reset" value="Cancelar">
        <input name="addsubmit" type="button" value="Adicionar">
        </td>
    </tr>
</table>
</form>
