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
            <input type="text" name="input_url" value="http://">
            <a id="check_url">verificar</a>
            <a id="change_url" style="display:none">modificar</a>
        </td>
    </tr>
    <tr>
        <td class="formlabel">Tipo de CMS:</td>
        <td><select name="cms_type" disabled></select></td>
    </tr>
    <tr>
        <td class="formlabel">Gerenciador:</td>
        <td><input type="text" name="manager_url" value="http://" disabled></td>
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
        <td><input type="password" name="manager_password"></td>
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
