$(document).ready(function()
{
    /* DEFAULTS */

    var active_request = false;
    var register = false;


    /* SWITCHES */

    /* spinner */

    function showSpinner()
    {
        $.ab_spinner
        ({
            height: 32, width: 32,
            image: "<?php img_src('spinner/linux_spinner.png') ?>",
            message: "... carregando"
        });
    }

    function hideSpinner()
    {
        $.ab_spinner_stop();
    }

    /* toggle between login and register form */

    function toggleForm()
    {
        $("#logintitle").toggle();
        $("#regtitle").toggle();
        $("#regrow").toggle();
        $("#pwdconfrow").toggle();
        $("input[@name='regcancel']").toggle();
        register = register ^ true;
    }


    /* ACTIONS */

    /* on error */

    function onError()
    {
        alert("<?php tr('server_error') ?>");
    }

    /* password recovery */

    function passwordRecovery()
    {
        if(active_request == true)
        {
            return null;
        }

        if($("input[@name='email']").val() == "")
        {
            $.ab_alert("<?php tr('recovery_email') ?>");
            return null;
        }

        parameters = { email: $("input[@name='email']").val() }

        $.ajax
        ({
            type: "POST",
            url: "<?php echo url('profile', 'recovery') ?>",
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
                var message = $(xml).find('message').text()

                if(message != "") $.ab_alert(message);
            }, 
            error: function () { onError(); } 
        });
    };

    /* form submit */

    function formSubmit()
    {
        if(active_request == true) return null;
        if(register == true) { registerSubmit(); } else { loginSubmit(); }
    }

    /* register */

    function registerSubmit()
    {
        email = $("input[@name='email']").val();
        password = $("input[@name='password']").val();
        confirm_ = $("input[@name='confirm']").val();

        if(email == "" || password == "" || confirm_ == "")
        {
            $.ab_alert("<?php tr('form_incomplete') ?>");
            return null;
        }

        if(password != confirm_)
        {
            $.ab_alert("<?php tr('form_not_match') ?>");
            return null;
        }

        parameters = { email: email, password: password, confirm: confirm_ }

        $.ajax
        ({
            type: "POST",
            url: "<?php url('profile', 'register') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function ()
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
                var message = $(xml).find('message').text()

                if(message != "") $.ab_alert(message);
            }, 
            error: function () { onError(); } 
        });
    };

    /* login */

    function loginSubmit()
    {
        email = $("input[@name='email']").val();
        password = $("input[@name='password']").val();

        if(email == "" || password == "")
        {
            $.ab_alert("<?php tr('form_incomplete') ?>");
            return null;
        }

        parameters = { email: email, password: password }

        $.ajax
        ({
            type: "POST",
            url: "<?php url('profile', 'login') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function ()
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
                var login = $(xml).find('login').text()
                var message = $(xml).find('message').text()

                if(login == "ok") window.location = "<?php url('dashboard') ?>";
                if(message != "") $.ab_alert(message);
            }, 
            error: function () { onError(); } 
        });
    };


    /* TRIGGERS */

    $("#reglnk").click(function()
    {
        toggleForm();
    });

    $("input[@name='regcancel']").click(function() 
    {
        toggleForm();
    });

    $("#pwdlnk").click(function()
    {
        passwordRecovery();
    });

    $("input[@name='frmsubmit']").click(function() 
    {
        formSubmit();
    });
});
