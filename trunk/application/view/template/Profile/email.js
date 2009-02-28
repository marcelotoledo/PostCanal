$(document).ready(function()
{
    /* DEFAULTS */

    var active_request = false;


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

    function onError()
    {
        alert('erro no servidor!!!');
    }

    function emailChange()
    {
        if(active_request == true)
        {
            return null;
        }

        email = $("input[@name='email']").val();
        uid = $("input[@name='uid']").val();
        password = $("input[@name='password']").val();

        if(password == "")
        {
            $.ab_alert("Preencha o formulário corretamente");
            return null;
        }

        parameters = { email: email, uid: uid, password: password }

        $.ajax
        ({
            type: "POST",
            url: "<?php $this->url('profile','email') ?>",
            dataType: "json",
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
                var result = data.result;

                if(result == "ok") 
                {
                    $("#emlform").toggle();
                    $("#changenotice").toggle();
                }
                else if(result == "failed") 
                {
                    $.ab_alert("Não foi possível alterar o e-mail de acesso");
                }
                else if(result == "unmatched_password") 
                {
                    $.ab_alert("Senha inválida");
                }
                else
                {
                    onError();
                }
            }, 
            error: function (data) { onError(); }
        });
    };


    /* TRIGGERS */

    $("input[@name='emlchangesubmit']").click(function() 
    {
        emailChange();
    });
});
