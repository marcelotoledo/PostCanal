$(document).ready(function()
{
    /* DEFAULTS */

    var ar = false; /* active request */

    /* spinner */

    $.b_spinner
    ({
        image: "<?php B_Helper::img_src('spinner.gif') ?>",
        message: "... <?php echo $this->translation->application_loading ?>"
    });

    /* SWITCHES */

    /* spinner */

    function sp(b)
    {
        ((ar = b) == true) ? $.b_spinner_start() : $.b_spinner_stop();
    }

    /* password change */

    function onError()
    {
        alert('erro no servidor!!!');
    }

    function emailChange()
    {
        if(ar == true)
        {
            return null;
        }

        email = $("input[name='email']").val();
        password = $("input[name='password']").val();
        user = $("input[name='user']").val();

        if(password == "")
        {
            $.ab_alert("Preencha o formulário corretamente");
            return null;
        }

        parameters = { email: email, password: password, user: user }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('profile','email') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function() { sp(true);  },
            complete: function()   { sp(false); },
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

    $("input[name='emlchangesubmit']").click(function() 
    {
        emailChange();
    });
});
