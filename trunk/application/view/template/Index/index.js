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
            image: "<?php B_Helper::img_src('spinner/linux_spinner.png') ?>",
            message: "... <?php echo $this->translation->application_loading ?>"
        });
    }

    function hideSpinner()
    {
        $.ab_spinner_stop();
    }

    /* toggle between login and register form */

    function toggleForm()
    {
        $("#ftitlog").toggle();
        $("#ftitreg").toggle();
        $("#lnkrow").toggle();
        $("#confirmrow").toggle();
        $("input[@name='regcancel']").toggle();
        $("#message").hide();
        register = register ^ true;
    }

    /* ACTIONS */

    /* show message */

    function showMessage(message)
    {
        $("#message td").html(message);
        $("#message").show();
    }

    /* on error */

    function onError()
    {
        alert("<?php echo $this->translation->server_error ?>");
    }

    /* password recovery */

    function passwordRecovery()
    {
        if($("input[@name='email']").val() == "")
        {
            showMessage("<?php echo $this->translation->recovery_email ?>");
            return null;
        }

        parameters = { email: $("input[@name='email']").val() }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('profile', 'recovery') ?>",
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
                var message = data.find('message').text();
                if(message != "") showMessage(message);
            }, 
            error: function () { onError(); } 
        });
    };

    /* form submit */

    function formSubmit()
    {
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
            showMessage("<?php echo $this->translation->form_incomplete ?>");
            return null;
        }

        if(password != confirm_)
        {
            showMessage("<?php echo $this->translation->form_not_match ?>");
            return null;
        }

        parameters = { email: email, password: password, confirm: confirm_ }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('profile', 'register') ?>",
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
                var data = $(xml).find('data');
                var register = data.find('register').text();
                var message = data.find('message').text();
                if(register == "true") toggleForm();
                if(message != "") showMessage(message);
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
            showMessage("<?php echo $this->translation->form_incomplete ?>");
            return null;
        }

        parameters = { email: email, password: password }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('profile', 'login') ?>",
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
                var data = $(xml).find('data');
                var login = data.find('login').text();
                var message = data.find('message').text();
                var url = "<?php B_Helper::url('dashboard') ?>";
                if(login == "true") window.location = url;
                if(message != "") showMessage(message);
            }, 
            error: function () { onError(); } 
        });
    };


    /* TRIGGERS */

    $("#reglnk").click(function()
    {
        if(active_request == false) { toggleForm(); }
    });

    $("input[@name='regcancel']").click(function() 
    {
        if(active_request == false) { toggleForm(); }
    });

    $("#pwdlnk").click(function()
    {
        if(active_request == false) { passwordRecovery(); }
    });

    $("input[@name='frmsubmit']").click(function() 
    {
        if(active_request == false) { formSubmit(); }
    });
});
