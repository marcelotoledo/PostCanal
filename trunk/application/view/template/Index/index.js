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
    
    /* submit form */

    function showSubmitResponse(response, status)
    {
        if(status == "success")
        {
            if(response == "login_ok") 
                window.location = "<?php echo BASE_URL ?>dashboard";
            else if(response == "login_incomplete") 
                alert("Autenticação INCOMPLETA");
            else if(response == "login_register_unconfirmed") 
                alert("Cadastro NÃO CONFIRMADO");
            else if(response == "login_invalid") 
                alert("Autenticação INVALIDA");

            else if(response == "register_ok") 
                alert("Cadastro OK");
            else if(response == "register_incomplete") 
                alert("Cadastro INCOMPLETO");
            else if(response == "register_password_unconfirmed") 
                alert("Senha NÃO CONFIRMADA");
            else if(response == "register_error") 
                alert("Cadastro ERRO!");
        }
        else
        {
            alert("ERRO INESPERADO");
        }
    }

    $("input[@name='authsubmit']").click(function() 
    {
        register = $("input[@name='register']").val();

        action = (register == "yes") ? "register" : "login";

        parameters = { email: $("input[@name='email']").val(),
                       password: $("input[@name='password']").val(),
                       confirm: $("input[@name='confirm']").val() };

        $.getJSON("<?php echo BASE_URL ?>profile/" + action, 
                  parameters, 
                  function(data, status)
        {
            showSubmitResponse(data.response, status);
        });
    });
});
