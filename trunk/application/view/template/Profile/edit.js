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

    /* email change */

    function setEmailChange(emlchange)
    {
        if(emlchange)
        {
            $("input[@name='login_email']").attr("disabled", false);
            $("input[@name='emlchangecancel']").attr("disabled", false);
            $("input[@name='emlchangesubmit']").attr("disabled", false);
            $("input[@name='login_email']").focus();
        }
        else
        {
            $("input[@name='login_email']").attr("disabled", true);
            $("input[@name='emlchangecancel']").attr("disabled", true);
            $("input[@name='emlchangesubmit']").attr("disabled", true);
        }
    }

    /* password change */

    function setPasswordChange(pwdchange)
    {
        if(pwdchange)
        {
            $("input[@name='current_password']").attr("disabled", false);
            $("input[@name='new_password']").attr("disabled", false);
            $("input[@name='confirm_password']").attr("disabled", false);
            $("input[@name='pwdchangecancel']").attr("disabled", false);
            $("input[@name='pwdchangesubmit']").attr("disabled", false);
            $("input[@name='current_password']").focus();
        }
        else
        {
            $("input[@name='current_password']").val("");
            $("input[@name='current_password']").attr("disabled", true);
            $("input[@name='new_password']").val("");
            $("input[@name='new_password']").attr("disabled", true);
            $("input[@name='confirm_password']").val("");
            $("input[@name='confirm_password']").attr("disabled", true);
            $("input[@name='pwdchangecancel']").attr("disabled", true);
            $("input[@name='pwdchangesubmit']").attr("disabled", true);
        }
    }


    /* ACTIONS */

    function onError()
    {
        // window.location = "<?php B_Helper::url('profile','edit') ?>";
        alert('error');
    }

    /* edit submit */

    function editSubmit()
    {
        if(active_request == true)
        {
            return null;
        }

        name = $("input[@name='name']").val();

        parameters = { name: name }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('profile', 'edit') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function ()
            {
                active_request = true;
                showSpinner();
            },
            complete: function ()
            {
                active_request = false;
                hideSpinner();
            },
            success: function (xml) 
            { 
                var data = $(xml).find('data');
                var saved = data.find('saved').text();
                var message = data.find('message').text();
                if(message != "") $.ab_alert(message);
            }, 
            error: function () { onError(); }
        });
    }

    /* password change submit */

    function passwordChangeSubmit()
    {
        if(active_request == true)
        {
            return null;
        }

        current = $("input[@name='current_password']").val();
        password = $("input[@name='new_password']").val();
        _confirm = $("input[@name='confirm_password']").val();

        if((current == "" || password == "" || _confirm == ""))
        {
            $.ab_alert("Preencha o formulário corretamente");
            return null;
        }

        if(password != _confirm)
        {
            $.ab_alert("Senha e confirmação NÃO CORRESPONDEM");
            return null;
        }

        parameters = { current:  current, 
                       password: password, 
                       confirm:  _confirm }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('profile', 'password') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function ()
            {
                active_request = true;
                showSpinner();
            },
            complete: function ()
            {
                active_request = false;
                hideSpinner();
            },
            success: function (xml) 
            { 
                var data = $(xml).find('data');
                var updated = data.find('updated').text();
                var message = data.find('message').text();
                if(message != "") $.ab_alert(message);

                if(updated == "true")
                {
                    setPasswordChange(false);
                }
            }, 
            error: function () { onError(); }
        });
    }

    /* email change submit */

    function emailChangeSubmit()
    {
        if(active_request == true)
        {
            return null;
        }

        new_email = $("input[@name='login_email']").val();

        if(new_email == "")
        {
            $.ab_alert("Preencha o formulário corretamente");
            return null;
        }

        parameters = { new_email: new_email }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('profile', 'email') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function ()
            {
                active_request = true;
                showSpinner();
            },
            complete: function ()
            {
                active_request = false;
                hideSpinner();
                setEmailChange(false);
            },
            success: function (xml) 
            { 
                var data = $(xml).find('data');
                var accepted = data.find('accepted').text();
                var message = data.find('message').text();
                if(message != "") $.ab_alert(message);
            }, 
            error: function () { onError(); }
        });
    }

    
    /* TRIGGERS */

    /* edit */

    $("input[@name='editsubmit']").click(function() 
    {
        editSubmit();
    });

    /* password change */

    $("#pwdchangelnk").click(function() 
    {
        setPasswordChange(true);
    });

    $("input[@name='pwdchangesubmit']").click(function() 
    {
        passwordChangeSubmit();
    });

    $("input[@name='pwdchangecancel']").click(function() 
    {
        setPasswordChange(false);
    });

    /* email change */

    $("#emlchangelnk").click(function() 
    {
        setEmailChange(true);
    });

    $("input[@name='emlchangesubmit']").click(function() 
    {
        emailChangeSubmit();
    });

    $("input[@name='emlchangecancel']").click(function() 
    {
        setEmailChange(false);
    });
});
