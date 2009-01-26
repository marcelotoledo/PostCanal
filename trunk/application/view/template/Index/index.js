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
        if($("input[@name='email']").val() == "")
        {
            simple_popup("digite um EMAIL");
            return null;
        }

        parameters = { email: $("input[@name='email']").val() }

        showSpinner();
        $.ajax
        ({
            type: "POST",
            url: "<?php echo AB_Request::url('profile', 'recovery') ?>",
            dataType: "json",
            data: parameters,
            success: function (message) 
            { 
                hideSpinner();
                result = message ? message.result : null;
                if(result == "recovery_ok") 
                    simple_popup("Um EMAIL foi enviado " + 
                                 "para o endereço informado");
                else if(result == "recovery_instruction_failed") 
                    simple_popup("Não foi possível enviar instruções " + 
                                 "para o endereço de e-mail especificado!");
            }, 
            error: function (message) 
            { 
                hideSpinner();
                simple_popup("ERRO NO SERVIDOR");
            }
        });
    });

    /* login / register */

    $("input[@name='authsubmit']").click(function() 
    {
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

        showSpinner();
        $("input[@name='authsubmit']").attr("disabled", true);

        $.ajax
        ({
            type: "POST",
            url: "<?php echo AB_Request::url('profile') ?>/" + action,
            dataType: "json",
            data: parameters,
            success: function (data) 
            { 
                $("input[@name='authsubmit']").attr("disabled", false);
                hideSpinner();

                result = data ? data.result : null;

                /* login */

                if(result == "login_ok") 
                {
                    window.location = "<?php echo AB_Request::url('dashboard') ?>";
                }
                else if(result == "login_invalid") 
                {
                    simple_popup("Autenticação INVÁLIDA");
                }
                else if(result == "login_register_unconfirmed") 
                {
                    simple_popup("Cadastro NÃO CONFIRMADO.<br>" + 
                          "Verifique o pedido de confirmação " + 
                          "enviada por e-mail.");
                }

                /* register */

                else if(result == "register_ok") 
                {
                    simple_popup("Cadastro realizado com sucesso.\n" + 
                          "Um EMAIL foi enviado para o endereço informado");
                    toggleAuthForm();
                }
                else if(result == "register_failed") 
                {
                    simple_popup("Não foi possível efetuar um novo cadastro");
                }
                else if(result == "register_incomplete") 
                {
                    simple_popup("Cadastro INCOMPLETO");
                }
                else if(result == "register_password_not_matched") 
                {
                    simple_popup("Senha e Confirmação NÃO CORRESPONDEM");
                }
                else if(result == "register_instruction_failed") 
                {
                    simple_popup("Não foi possível enviar instruções " + 
                          "para o endereço de e-mail especificado!");
                }
            }, 
            error: function (data) 
            { 
                $("input[@name='authsubmit']").attr("disabled", false);
                hideSpinner();
                simple_popup("ERRO NO SERVIDOR");
            }
        });
    });
});
