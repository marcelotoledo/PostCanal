<table id="container">
    <tr>
        <td id="bigtitle"><span>Blotomate</span></td>
        <td>
            <div id="logintitle"><h1><?php tr('form_login') ?></h1></div>
            <div id="regtitle" style="display:none"><h1><?php tr('form_register') ?></h1></div>
            <form>
            <table>
                <tr>
                    <td class="formlabel"><?php tr('form_email') ?>:</td>
                    <td><input type="text" name="email"></td>
                </tr>
                <tr>
                    <td class="formlabel"><?php tr('form_password') ?>:</td>
                    <td><input type="password" name="password"></td>
                </tr>
                <tr id="regrow">
                    <td>&nbsp;</td>
                    <td>
                        <a id="reglnk"><?php tr('form_not_registered') ?></a> | 
                        <a id="pwdlnk"><?php tr('form_forgot_password') ?></a>
                    </td>
                </tr>
                <tr id="pwdconfrow" style="display:none">
                    <td class="formlabel"><?php tr('form_confirm') ?>:</td>
                    <td><input type="password" name="confirm"></td>
                </tr>
                <tr class="formbutton">
                    <td>&nbsp;</td>
                    <td>
                        <input name="regcancel" type="button" value="<?php tr('form_cancel') ?>" style="display:none">
                        <input name="frmsubmit" type="button" value="<?php tr('form_submit') ?>">
                    </td>
                </tr>
            </table>
            </form>
        </td>
    </tr>
</table>
