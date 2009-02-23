$(document).ready(function()
{
    /* DEFAULTS */

    var active_request = false;

    $("input[@name='register']").val("no");


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

    /* toggle between authentication and register form */

    function toggleAuthForm()
    {
        register = $("input[@name='register']").val();

        $("#authtitle").toggle();
        $("#regtitle").toggle();
        $("#regrow").toggle();
        $("#pwdconfrow").toggle();
        $("input[@name='regcancel']").toggle();
        $("input[@name='register']").val(register == "yes" ? "no" : "yes");
    }


    /* ACTIONS */

    /* on error */

    function onError()
    {
        alert('erro no servidor!');
    }

    /* password recovery */

    function passwordRecovery()
    {
        if(active_request == true)
        {
            return null;
        }

        if($("input[@name='email']").val() == "")
        {
            $.ab_alert("digite um EMAIL");
            return null;
        }

        parameters = { email: $("input[@name='email']").val() }

        $.ajax
        ({
            type: "POST",
            url: "<?php echo $this->url('profile', 'recovery') ?>",
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
                    $.ab_alert("Um EMAIL foi enviado para o endereço informado");
                }
                else
                {
                    $.ab_alert("Não foi possível enviar instruções " + 
                               "para o endereço de e-mail especificado!");
                }
            }, 
            error: function () { onError(); } 
        });
    };

    /* login / register */

    function authSubmit()
    {
        if(active_request == true)
        {
            return null;
        }

        register = $("input[@name='register']").val();
        email = $("input[@name='email']").val();
        password = $("input[@name='password']").val();
        confirm_ = $("input[@name='confirm']").val();

        if(email == "" || password == "" || 
          (register == "yes" && confirm_ == ""))
        {
            $.ab_alert("Preencha o formulário corretamente");
            return null;
        }

        if(register == "yes" && password != confirm_)
        {
            $.ab_alert("Senha e confirmação NÃO CORRESPONDEM");
            return null;
        }

        action = (register == "yes") ? "register" : "login";
        parameters = { email: email, password: password, confirm: confirm_ }

        $.ajax
        ({
            type: "POST",
            url: "<?php $this->url('profile') ?>/" + action,
            dataType: "json",
            data: parameters,
            beforeSend: function ()
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

                /* login */

                if(result == "logged") 
                {
                    window.location = "<?php $this->url('dashboard') ?>";
                }
                else if(result == "invalid") 
                {
                    $.ab_alert("Autenticação INVÁLIDA");
                }
                else if(result == "unconfirmed") 
                {
                    $.ab_alert("Cadastro NÃO CONFIRMADO.<br>" + 
                               "Verifique o pedido de confirmação enviado por e-mail.");
                }

                /* register */

                else if(result == "registered") 
                {
                    $.ab_alert("Cadastro realizado com sucesso.\n" + 
                        "Um EMAIL foi enviado para o endereço informado");
                    toggleAuthForm();
                }
                else if(result == "failed") 
                {
                    $.ab_alert("Não foi possível efetuar um novo cadastro");
                }
                else if(result == "incomplete") 
                {
                    $.ab_alert("Cadastro INCOMPLETO");
                }
                else if(result == "unmatched_password") 
                {
                    $.ab_alert("Senha e Confirmação NÃO CORRESPONDEM");
                }
                else if(result == "instruction_failed") 
                {
                    $.ab_alert("Não foi possível enviar instruções " + 
                               "para o endereço de e-mail especificado!");
                }
                else
                {
                    onError();
                }
            }, 
            error: function () { onError(); } 
        });
    };


    /* TRIGGERS */

    $("#reglnk").click(function()
    {
        toggleAuthForm();
    });

    $("input[@name='regcancel']").click(function() 
    {
        toggleAuthForm();
    });

    $("#pwdlnk").click(function()
    {
        passwordRecovery();
    });

    $("input[@name='authsubmit']").click(function() 
    {
        authSubmit();
    });
});
