<table id="container">
    <tr>
        <td id="bigtitle"><span>Blotomate</span></td>
        <td>
            <form id="authform">
            <input type="hidden" name="register" value="no">
            <table>
                <tr>
                    <td class="formtitle">
                        <span id="authtitle">Autenticação</span>
                        <span id="regtitle" style="display:none">Novo Cadastro</span>
                    </td>
                    <td class="formloading">
                        <div id="spinner">&nbsp;</div>
                    </td>
                </tr>
                <tr>
                    <td class="formlabel">E-mail:</td>
                    <td><input type="text" name="email"></td>
                </tr>
                <tr>
                    <td class="formlabel">Senha:</td>
                    <td><input type="password" name="password"></td>
                </tr>
                <tr id="regrow">
                    <td>&nbsp;</td>
                    <td>
                        <a id="reglnk">não cadastrado</a> | 
                        <a id="pwdlnk">senha?</a>
                    </td>
                </tr>
                <tr id="pwdconfrow" style="display:none">
                    <td class="formlabel">Confirmar Senha:</td>
                    <td><input type="password" name="confirm"></td>
                </tr>
                <tr class="formbutton">
                    <td>&nbsp;</td>
                    <td>
                        <input name="regcancel" type="button" value="Cancelar" style="display:none">
                        <input name="authsubmit" type="button" value="Enviar">
                    </td>
                </tr>
            </table>
            </form>
        </td>
    </tr>
</table>
