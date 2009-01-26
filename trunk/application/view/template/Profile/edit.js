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

    /* password change */

    $("input[@name='pwdchangesubmit']").click(function() 
    {
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

        showSpinner();
        $("input[@name='pwdchangesubmit']").attr("disabled", true);

        $.ajax
        ({
            type: "POST",
            url: "<?php echo AB_Request::url('profile','passwordChange') ?>",
            dataType: "json",
            data: parameters,
            success: function (data) 
            { 

                $("input[@name='pwdchangesubmit']").attr("disabled", false);
                hideSpinner();

                result = data ? data.result : null;

                if(result == "password_change_ok") 
                {
                }
                else if(result == "password_change_failed") 
                {
                    simple_popup("Não foi possível alterar a senha de acesso");
                }
                else if(result == "password_change_not_matched") 
                {
                    simple_popup("Senha e Confirmação NÃO CORRESPONDEM");
                }
            }, 
            error: function (data) 
            { 
                $("input[@name='pwdchangesubmit']").attr("disabled", false);
                hideSpinner();
                simple_popup("ERRO NO SERVIDOR");
            }
        });
    });
});
