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

    $("input[@name='pwdchangesubmit']").click(function() 
    {
        if(active_request == true)
        {
            return null;
        }

        uid = $("input[@name='uid']").val();
        password = $("input[@name='password']").val();
        confirm_ = $("input[@name='confirm']").val();

        if(password == "" || confirm_ == "")
        {
            simple_popup("Preencha o formulário corretamente");
            return null;
        }

        if(password != confirm_)
        {
            simple_popup("Senha e confirmação NÃO CORRESPONDEM");
            return null;
        }

        parameters = { uid: uid, password: password, confirm: confirm_ }

        $.ajax
        ({
            type: "POST",
            url: "<?php $this->url('profile','passwordChange') ?>",
            dataType: "text",
            data: parameters,
            beforeSend: function()
            {
                active_request = true;
                showSpinner();
            },
            complete: function()
            {
                active_request = false;
                hideSpinner();
            },
            success: function (data) 
            { 
                if(data == "password_change_ok") 
                {
                    $("#pwdform").toggle();
                    $("#changenotice").toggle();
                }
                else if(data == "password_change_failed") 
                {
                    simple_popup("Não foi possível alterar a senha de acesso");
                }
                else if(data == "password_change_not_matched") 
                {
                    simple_popup("Senha e Confirmação NÃO CORRESPONDEM");
                }
            }, 
            error: function (data) 
            { 
                simple_popup("ERRO NO SERVIDOR");
            }
        });
    });
});
