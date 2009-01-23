<input type="hidden" name="uid" value="<?php echo $this->profile->getUID() ?>">
<table>
    <tr>
        <td class="formtitle">
            <span id="authtitle">Editar perfil</span>
        </td>
        <td class="formloading">
            <div id="spinner">&nbsp;</div>
        </td>
    </tr>
    <tr>
        <td class="formlabel">Nome:</td>
        <td><input type="text" name="name"></td>
    </tr>
</table>
<br>
<table>
    <tr>
        <td class="formlabel">&nbsp;</td>
        <td><a href="#">trocar senha</a></td>
    </tr>
</table>    
<table id="passwordform">
    <tr>
        <td class="formlabel">Senha atual:</td>
        <td><input type="text" name="name"></td>
    </tr>
    <tr>
        <td class="formlabel">Nova senha:</td>
        <td><input type="text" name="name"></td>
    </tr>
    <tr>
        <td class="formlabel">Confirmar senha:</td>
        <td><input type="text" name="name"></td>
    </tr>
    <tr class="formbutton">
        <td>&nbsp;</td>
        <td>
        <input name="editcancel" type="button" value="Cancelar">
        <input name="editsubmit" type="button" value="Alterar">
        </td>
    </tr>
</table>
