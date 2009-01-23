$(document).ready(function()
{
    /* spinner */

    function showSpinner()
    {
        $("#spinner").spinner
        ({
            height: 32, width: 32, speed: 50,
            image: '/image/spinner/linux_spinner.png'
        });
    }

    function hideSpinner()
    {
        $.spinnerStop();
        $("#spinner").attr("style", "");
    }

    /* default value for register */

    $("input[@name='register']").val("no");

    /* toggle between authentication and register forms */

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
            url: "<?php echo BASE_URL ?>/profile/recovery",
            dataType: "json",
            data: parameters,
            success: function (message) 
            { 
                hideSpinner();
                response = message ? message.response : null;
                if(response == "recovery_ok") 
                    simple_popup("Um EMAIL foi enviado " + 
                                 "para o endereço informado");
                else if(response == "recovery_instruction_failed") 
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
            url: "<?php echo BASE_URL ?>/profile/" + action,
            dataType: "json",
            data: parameters,
            success: function (data) 
            { 
                $("input[@name='authsubmit']").attr("disabled", false);
                hideSpinner();

                response = data ? data.response : null;

                /* login */

                if(response == "login_ok") 
                {
                    window.location = "<?php echo BASE_URL ?>/dashboard";
                }
                else if(response == "login_invalid") 
                {
                    simple_popup("Autenticação INVÁLIDA");
                }
                else if(response == "login_register_unconfirmed") 
                {
                    simple_popup("Cadastro NÃO CONFIRMADO.<br>" + 
                          "Verifique o pedido de confirmação " + 
                          "enviada por e-mail.");
                }

                /* register */

                else if(response == "register_ok") 
                {
                    simple_popup("Cadastro realizado com sucesso.\n" + 
                          "Um EMAIL foi enviado para o endereço informado");
                    toggleAuthForm();
                }
                else if(response == "register_failed") 
                {
                    simple_popup("Não foi possível efetuar um novo cadastro");
                }
                else if(response == "register_incomplete") 
                {
                    simple_popup("Cadastro INCOMPLETO");
                }
                else if(response == "register_password_not_matched") 
                {
                    simple_popup("Senha e Confirmação NÃO CORRESPONDEM");
                }
                else if(response == "register_instruction_failed") 
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
