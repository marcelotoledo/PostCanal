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
    
    /* submit form */

    function showSubmitResponse(response)
    {
        if(response == "reg_ok") alert("Cadastro OK. Verifique o e-mail");
        else if(response == "reg_err") alert("Erro ao cadastrar");
        else if(response == "chk_ok") alert("Autenticação OK");
        else if(response == "chk_err") alert("Erro ao autenticar");
        else alert("Erro desconhecido");
    }

    $("input[@name='authsubmit']").click(function() 
    {
        parameters = { register: $("input[@name='register']").val(),
                       email: $("input[@name='email']").val(),
                       password: $("input[@name='password']").val(),
                       confirm: $("input[@name='confirm']").val() };

        $.getJSON("/index/authentication", parameters, function(data, status)
        {
            if(status == "success") showSubmitResponse(data.response);
        });
    });
});
