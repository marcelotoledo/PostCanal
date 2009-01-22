$(document).ready(function()
{
    /* password change */

    $("input[@name='pwdchangesubmit']").click(function() 
    {
        uid = $("input[@name='uid']").val();
        password = $("input[@name='password']").val();
        confirm_ = $("input[@name='confirm']").val();

        if(password == "" || confirm_ == "")
        {
            alert("Preencha o formulário corretamente");
            return null;
        }

        if(password != confirm_)
        {
            alert("Senha e confirmação NÃO CORRESPONDEM");
            return null;
        }

        parameters = { uid: uid, password: password, confirm: confirm_ }

        document.body.style.cursor='wait';

        $.ajax
        ({
            type: "POST",
            url: "<?php echo BASE_URL ?>/profile/password",
            dataType: "json",
            data: parameters,
            success: function (data) 
            { 
                document.body.style.cursor='auto';
                response = data ? data.response : null;

                if(response == "password_change_ok") 
                {
                    alert("Senha alterada com sucesso!");
                    window.location = "<?php echo BASE_URL ?>";
                }
                else if(response == "password_change_failed") 
                {
                    alert("Não foi possível alterar a senha de acesso");
                }
                else if(response == "password_change_not_matched") 
                {
                    alert("Senha e Confirmação NÃO CORRESPONDEM");
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
