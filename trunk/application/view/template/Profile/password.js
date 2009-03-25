$(document).ready(function()
{
    /* DEFAULTS */

    var ar = false;

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

    /* ACTIONS */

    /* show message */

    function showMessage(message)
    {
        $("#message td").html(message);
        $("#message").show();
    }

    /* password change */

    function onError()
    {
        alert('erro no servidor!!!');
    }

    function passwordChange()
    {
        if(ar == true)
        {
            return null;
        }

        email = $("input[name='email']").val();
        password = $("input[name='password']").val();
        confirm_ = $("input[name='confirm']").val();
        user = $("input[name='user']").val();

        if(password == "" || confirm_ == "")
        {
            showMessage("Preencha o formulário corretamente");
            return null;
        }

        if(password != confirm_)
        {
            showMessage("Senha e confirmação NÃO CORRESPONDEM");
            return null;
        }

        parameters = { email: email, password: password, confirm: confirm_, user: user }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('profile','password') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function() { sp(true);  },
            coomplete: function()  { sp(false); },
            success: function (xml) 
            { 
                var data = $(xml).find('data');
                var updated = data.find('updated').text();
                var message = data.find('message').text();

                if(updated == "true") 
                {
                    $("#pwdform").toggle();
                    $("#changenotice").toggle();
                }
                else
                {
                    if(message != "") showMessage(message);
                }
            }, 
            error: function (data) { onError(); }
        });
    };


    /* TRIGGERS */

    $("input[name='pwdchangesubmit']").click(function() 
    {
        passwordChange();
    });
});
