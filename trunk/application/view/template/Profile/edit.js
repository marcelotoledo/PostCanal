$(document).ready(function()
{
    var active_request = false;

    /* spinner */

    function showSpinner()
    {
        $("#spinner").spinner
        ({
            height: 32, width: 32, speed: 50,
            image: "<?php $this->img_src('spinner/linux_spinner.png') ?>"
        });
    }

    function hideSpinner()
    {
        $.spinnerStop();
        $("#spinner").attr("style", "");
    }

    /* password change */

    function setPasswordChange(pwdchange)
    {
        if(pwdchange)
        {
            $("input[@name='current_password']").attr("disabled", false);
            $("input[@name='new_password']").attr("disabled", false);
            $("input[@name='new_password_confirm']").attr("disabled", false);
            $("input[@name='current_password']").focus();
            $("input[@name='pwdchange']").val("yes");
        }
        else
        {
            $("input[@name='current_password']").attr("disabled", true);
            $("input[@name='current_password']").val("");
            $("input[@name='new_password']").attr("disabled", true);
            $("input[@name='new_password']").val("");
            $("input[@name='new_password_confirm']").attr("disabled", true);
            $("input[@name='new_password_confirm']").val("");
            $("input[@name='pwdchange']").val("no");
        }
    }

    setPasswordChange(false);

    $("#pwdchangelnk").click(function() 
    {
        setPasswordChange(true);
    });

    /* cancel */

    $("input[@name='editcancel']").click(function() 
    {
        setPasswordChange(false);
    });

    /* submit */

    $("input[@name='editsubmit']").click(function() 
    {
        if(active_request == true)
        {
            return null;
        }

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

        $.ajax
        ({
            type: "POST",
            url: "<?php $this->url('profile', 'editSave') ?>",
            dataType: "text",
            data: parameters,
            beforeSend: function ()
            {
                active_request = true;
                showSpinner();
            },
            complete: function ()
            {
                active_request = false;
                setPasswordChange(false);
                hideSpinner();
            },
            success: function (data) 
            { 
                if(data == "edit_save_ok") 
                {
                    simple_popup("Perfil alterado com sucesso");
                }
                else if(data == "edit_save_failed")
                {
                    simple_popup("Alteração do perfil FALHOU!");
                }
                else if(data == "edit_save_password_not_matched")
                {
                    simple_popup("Senha e confirmação NÃO CORRESPONDEM");
                }
                else if(data == "edit_save_wrong_password")
                {
                    simple_popup("Senha incorreta!");
                }
            }, 
            error: function () 
            { 
                window.location = "<?php $this->url('dashboard') ?>";
            }
        });
    });
});
