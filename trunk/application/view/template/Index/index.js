$(document).ready(function()
{
    /* default value for register */

    $("input[@name='register']").val("no");

    /* toggle between authentication and register forms */

    function toggleAuthForm()
    {
        register = $("input[@name='register']").val();

        $("#authtitlerow").toggle();
        $("#regtitlerow").toggle();
        $("#regrow").toggle();
        $("#pwdconfrow").toggle();
        $("input[@name='register']").val(register == "yes" ? "no" : "yes");
    }

    $("#reglnk").click(function()
    {
        toggleAuthForm();
    });

    $("#canlnk").click(function()
    {
        toggleAuthForm();
    });

    /* password recovery */

    $("#pwdlnk").click(function()
    {
        if($("input[@name='email']").val() == "")
        {
            alert("digite um EMAIL");
            return null;
        }

        parameters = { email: $("input[@name='email']").val() }

        document.body.style.cursor='wait';

        $.ajax
        ({
            type: "POST",
            url: "<?php echo BASE_URL ?>/profile/recovery",
            dataType: "json",
            data: parameters,
            success: function (message) 
            { 
                document.body.style.cursor='auto';
                response = message ? message.response : null;
                if(response == "recovery_ok") 
                    alert("Um EMAIL foi enviado para o endereço informado");
                else if(response == "recovery_instruction_failed") 
                    alert("Não foi possível enviar instruções " + 
                          "para o endereço de e-mail especificado!");
            }, 
            error: function (message) 
            { 
                document.body.style.cursor='auto';
                alert("ERRO NO SERVIDOR");
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
            alert("Preencha o formulário corretamente");
            return null;
        }

        if(register == "yes" && password != confirm_)
        {
            alert("Senha e confirmação NÃO CORRESPONDEM");
            return null;
        }

        action = (register == "yes") ? "register" : "login";
        parameters = { email: email, password: password, confirm: confirm_ }

        document.body.style.cursor='wait';

        $.ajax
        ({
            type: "POST",
            url: "<?php echo BASE_URL ?>/profile/" + action,
            dataType: "json",
            data: parameters,
            success: function (data) 
            { 
                document.body.style.cursor='auto';
                response = data ? data.response : null;

                /* login */

                if(response == "login_ok") 
                {
                    window.location = "<?php echo BASE_URL ?>/dashboard";
                }
                else if(response == "login_invalid") 
                {
                    alert("Autenticação INVALIDA");
                }
                else if(response == "login_register_unconfirmed") 
                {
                    alert("Cadastro NÃO CONFIRMADO.\n" + 
                          "Verifique o pedido de confirmação " + 
                          "enviada por e-mail.");
                }

                /* register */

                else if(response == "register_ok") 
                {
                    alert("Cadastro realizado com sucesso.\n" + 
                          "Um EMAIL foi enviado para o endereço informado");
                    toggleAuthForm();
                }
                else if(response == "register_failed") 
                {
                    alert("Não foi possível efetuar um novo cadastro");
                }
                else if(response == "register_incomplete") 
                {
                    alert("Cadastro INCOMPLETO");
                }
                else if(response == "register_password_not_matched") 
                {
                    alert("Senha e Confirmação NÃO CORRESPONDEM");
                }
                else if(response == "register_instruction_failed") 
                {
                    alert("Não foi possível enviar instruções " + 
                          "para o endereço de e-mail especificado!");
                }
            }, 
            error: function (data) 
            { 
                document.body.style.cursor='auto';
                alert("ERRO NO SERVIDOR");
            }
        });
    });
});
