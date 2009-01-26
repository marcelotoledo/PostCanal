$(document).ready(function()
{
    /* spinner */

    function showSpinner()
    {
        $("#spinner").spinner
        ({
            height: 32, width: 32, speed: 50,
            image: "<?php echo BASE_URL ?>" + 
                   "/image/spinner/linux_spinner.png"
        });
    }

    function hideSpinner()
    {
        $.spinnerStop();
        $("#spinner").attr("style", "");
    }

    /* password change */

    $("input[@name='pwdchange']").val("no");

    function enablePasswordChange()
    {
        $("input[@name='current_password']").attr("disabled", false);
        $("input[@name='new_password']").attr("disabled", false);
        $("input[@name='new_password_confirm']").attr("disabled", false);
        $("input[@name='current_password']").focus();
        $("input[@name='pwdchange']").val("yes");
    }

    $("#pwdchangelnk").click(function() 
    {
        enablePasswordChange();
    });

    /* cancel */

    $("input[@name='editcancel']").click(function() 
    {
        window.location = "<?php echo AB_Request::url('dashboard') ?>";
    });

    /* submit */

    $("input[@name='editsubmit']").click(function() 
    {
        name = $("input[@name='name']").val();
        pwdchange = $("input[@name='pwdchange']").val();
        current_password = $("input[@name='current_password']").val();
        new_password = $("input[@name='new_password']").val();
        new_password_confirm = $("input[@name='new_password_confirm']").val();

        if(pwdchange == "yes" && 
            (current_password == "" || new_password == "" || 
             new_password_confirm == ""))
        {
            simple_popup("Preencha o formulário corretamente");
            return null;
        }

        if(pwdchange == "yes" && new_password != new_password_confirm)
        {
            simple_popup("Senha e confirmação NÃO CORRESPONDEM");
            return null;
        }

        parameters = { name: name, 
                       pwdchange: pwdchange, 
                       current_password: current_password, 
                       new_password: new_password, 
                       new_password_confirm: new_password_confirm }

        showSpinner();
        $("input[@name='editsubmit']").attr("disabled", true);

        $.ajax
        ({
            type: "POST",
            url: "<?php echo AB_Request::url('profile', 'editSave') ?>",
            dataType: "json",
            data: parameters,
            success: function (data) 
            { 
                $("input[@name='editsubmit']").attr("disabled", false);
                hideSpinner();

                result = data ? data.result : null;

                if(result == "edit_ok") 
                {
                    simple_popup("OK");
                }
            }, 
            error: function (data) 
            { 
                $("input[@name='editsubmit']").attr("disabled", false);
                hideSpinner();
                simple_popup("ERRO NO SERVIDOR");
            }
        });
    });
});
