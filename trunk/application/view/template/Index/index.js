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

    /* default value for register */

    $("input[@name='register']").val("no");

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

    $("#reglnk").click(function()
    {
        toggleAuthForm();
    });

    $("input[@name='regcancel']").click(function() 
    {
        toggleAuthForm();
    });

    /* password recovery */

    $("#pwdlnk").click(function()
    {
        if(active_request == true)
        {
            return null;
        }

        if($("input[@name='email']").val() == "")
        {
            simple_popup("digite um EMAIL");
            return null;
        }

        parameters = { email: $("input[@name='email']").val() }

        $.ajax
        ({
            type: "POST",
            url: "<?php echo $this->url('profile', 'recovery') ?>",
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
                if(data == "recovery_ok") 
                    simple_popup("Um EMAIL foi enviado " + 
                                 "para o endereço informado");
                else if(data == "recovery_instruction_failed") 
                    simple_popup("Não foi possível enviar instruções " + 
                                 "para o endereço de e-mail especificado!");
            }, 
            error: function () 
            { 
                simple_popup("ERRO NO SERVIDOR");
            }
        });
    });

    /* login / register */

    $("input[@name='authsubmit']").click(function() 
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
            simple_popup("Preencha o formulário corretamente");
            return null;
        }

        if(register == "yes" && password != confirm_)
        {
            simple_popup("Senha e confirmação NÃO CORRESPONDEM");
            return null;
        }

        action = (register == "yes") ? "register" : "login";
        parameters = { email: email, password: password, confirm: confirm_ }

        $.ajax
        ({
            type: "POST",
            url: "<?php $this->url('profile') ?>/" + action,
            dataType: "text",
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
                /* login */

                if(data == "login_ok") 
                {
                    window.location = "<?php $this->url('dashboard') ?>";
                }
                else if(data == "login_invalid") 
                {
                    simple_popup("Autenticação INVÁLIDA");
                }
                else if(data == "login_register_unconfirmed") 
                {
                    simple_popup("Cadastro NÃO CONFIRMADO.<br>" + 
                          "Verifique o pedido de confirmação " + 
                          "enviada por e-mail.");
                }

                /* register */

                else if(data == "register_ok") 
                {
                    simple_popup("Cadastro realizado com sucesso.\n" + 
                          "Um EMAIL foi enviado para o endereço informado");
                    toggleAuthForm();
                }
                else if(data == "register_failed") 
                {
                    simple_popup("Não foi possível efetuar um novo cadastro");
                }
                else if(data == "register_incomplete") 
                {
                    simple_popup("Cadastro INCOMPLETO");
                }
                else if(data == "register_password_not_matched") 
                {
                    simple_popup("Senha e Confirmação NÃO CORRESPONDEM");
                }
                else if(data == "register_instruction_failed") 
                {
                    simple_popup("Não foi possível enviar instruções " + 
                          "para o endereço de e-mail especificado!");
                }
            }, 
            error: function () 
            { 
                simple_popup("ERRO NO SERVIDOR");
            }
        });
    });
});
