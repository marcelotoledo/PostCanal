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
            image: "<?php B_Helper::img_src('spinner/linux_spinner.png') ?>",
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
            url: "<?php B_Helper::url('profile','email') ?>",
            dataType: "xml",
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
            success: function (xml) 
            { 
                var data = $(xml).find('data');
                var accepted = data.find('accepted').text();
                var message = data.find('message').text();

                if(accepted == "true") 
                {
                    $("#emlform").toggle();
                    $("#changenotice").toggle();
                }
                else
                {
                    if(message != "") $.ab_alert(message);
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
