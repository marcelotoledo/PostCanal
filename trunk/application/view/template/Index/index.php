<table style="padding:25px">
    <tr>
        <td><span style="font-size:48;padding:50px;">Autoblog</span></td>
        <td>
            <!-- { login / register form -->
            <input type="hidden" name="register" value="no">
            <table>
                <tr id="authtitlerow">
                    <td colspan="2" class="formtitle">
                        <span class="formtitle">Autenticação</span>
                    </td>
                </tr>
                <tr id="regtitlerow" style="display:none">
                    <td colspan="2" class="formtitle">
                        <span class="formtitle">Novo Cadastro</span>
                        [<a href="#" id="canlnk">cancelar</a>]
                    </td>
                </tr>
                <tr>
                    <td class="fieldlabel">E-mail:</td>
                    <td><input type="text" name="email"></td>
                </tr>
                <tr>
                    <td class="fieldlabel">Senha:</td>
                    <td><input type="password" name="password"></td>
                </tr>
                <tr id="regrow">
                    <td>&nbsp;</td>
                    <td>
                        <a href="#" id="reglnk">não cadastrado</a> | 
                        <a href="#" id="pwdlnk">senha?</a>
                    </td>
                </tr>
                <tr id="pwdconfrow" style="display:none">
                    <td class="fieldlabel">Confirmar Senha:</td>
                    <td><input type="password" name="confirm"></td>
                </tr>
                <tr style="height:50px;vertical-align:bottom">
                    <td>&nbsp;</td>
                    <td style="text-align:right"><input name="authsubmit" type="button" value="Enviar"></td>
                </tr>
            </table>
            <!-- login / register form } -->
        </td>
    </tr>
</table>
