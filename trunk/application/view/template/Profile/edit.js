$(document).ready(function()
{
    /* DEFAULTS */

    var active_request = false;
    var password_change = false;


    /* SWITCHES */

    /* spinner */

    function showSpinner()
    {
        $.ab_spinner
        ({
            height: 32, width: 32,
            image: "<?php $this->img_src('spinner/linux_spinner.png') ?>",
            message: "... carregando"
        });
    }

    function hideSpinner()
    {
        $.ab_spinner_stop();
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

    setPasswordChange(password_change);


    /* ACTIONS */

    function onError()
    {
        window.location = "<?php $this->url('profile','edit') ?>";
    }

    function editSubmit()
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
            $.ab_alert("Preencha o formulário corretamente");
            return null;
        }

        if(pwdchange == "yes" && new_password != new_password_confirm)
        {
            $.ab_alert("Senha e confirmação NÃO CORRESPONDEM");
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
            url: "<?php $this->url('profile', 'edit') ?>",
            dataType: "json",
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
                result = data.result;

                if(result == "ok") 
                {
                    $.ab_alert("Perfil alterado com sucesso");
                }
                else if(result == "failed")
                {
                    $.ab_alert("Alteração do perfil FALHOU!");
                }
                else if(result == "unmatched_password")
                {
                    $.ab_alert("Senha e confirmação NÃO CORRESPONDEM");
                }
                else if(result == "wrong_password")
                {
                    $.ab_alert("Senha incorreta!");
                }
                else
                {
                    onError();
                }
            }, 
            error: function () { onError(); }
        });
    }

    
    /* TRIGGERS */

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
        editSubmit();
    });
});
