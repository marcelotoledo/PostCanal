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

    /* email change */

    function setEmailChange(emlchange)
    {
        if(emlchange)
        {
            $("input[name='login_email']").attr("disabled", false);
            $("input[name='emlchangecancel']").attr("disabled", false);
            $("input[name='emlchangesubmit']").attr("disabled", false);
            $("input[name='login_email']").focus();
        }
        else
        {
            $("input[name='login_email']").attr("disabled", true);
            $("input[name='emlchangecancel']").attr("disabled", true);
            $("input[name='emlchangesubmit']").attr("disabled", true);
        }
    }

    /* password change */

    function setPasswordChange(pwdchange)
    {
        if(pwdchange)
        {
            $("input[name='current_password']").attr("disabled", false);
            $("input[name='new_password']").attr("disabled", false);
            $("input[name='confirm_password']").attr("disabled", false);
            $("input[name='pwdchangecancel']").attr("disabled", false);
            $("input[name='pwdchangesubmit']").attr("disabled", false);
            $("input[name='current_password']").focus();
        }
        else
        {
            $("input[name='current_password']").val("");
            $("input[name='current_password']").attr("disabled", true);
            $("input[name='new_password']").val("");
            $("input[name='new_password']").attr("disabled", true);
            $("input[name='confirm_password']").val("");
            $("input[name='confirm_password']").attr("disabled", true);
            $("input[name='pwdchangecancel']").attr("disabled", true);
            $("input[name='pwdchangesubmit']").attr("disabled", true);
        }
    }


    /* ACTIONS */

    function err()
    {
        // window.location = "<?php B_Helper::url('profile','edit') ?>";
        alert('error');
    }

    /* edit submit */

    function editmsg(m)
    {
        _id = "editmessage"; $("#" + _id + " td").html(m); _tr = $("#" + _id);
        (m=="") ? (_tr.hide()) : (_tr.show());
    }

    function editSubmit()
    {
        if(ar == true)
        {
            return null;
        }

        name = $("input[name='name']").val();

        parameters = { name: name }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('profile', 'edit') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function () { sp(true); editmsg("");  },
            complete: function ()   { sp(false); },
            success: function (xml) 
            { 
                data = $(xml).find('data');
                saved = data.find('saved').text();
                message = data.find('message').text();
                editmsg(message);
            }, 
            error: function () { err(); }
        });
    }

    /* password change submit */

    function pwdchangemsg(m)
    {
        _id = "pwdchangemessage"; $("#" + _id + " td").html(m); _tr = $("#" + _id);
        (m=="") ? (_tr.hide()) : (_tr.show());
    }

    function passwordChangeSubmit()
    {
        if(ar == true)
        {
            return null;
        }

        current = $("input[name='current_password']").val();
        password = $("input[name='new_password']").val();
        _confirm = $("input[name='confirm_password']").val();

        if((current == "" || password == "" || _confirm == ""))
        {
            pwdchangemsg("<?php echo $this->translation->form_unfilled ?>");
            return null;
        }

        if(password != _confirm)
        {
            pwdchangemsg("<?php echo $this->translation->form_unconfirmed ?>");
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
            beforeSend: function () { sp(true); pwdchangemsg(""); },
            complete: function ()   { sp(false); },
            success: function (xml) 
            { 
                data = $(xml).find('data');
                updated = data.find('updated').text();
                message = data.find('message').text();
                pwdchangemsg(message);

                if(updated == "true")
                {
                    setPasswordChange(false);
                }
            }, 
            error: function () { err(); }
        });
    }

    /* email change submit */

    function emlchangemsg(m)
    {
        _id = "emlchangemessage"; $("#" + _id + " td").html(m); _tr = $("#" + _id);
        (m=="") ? (_tr.hide()) : (_tr.show());
    }

    function emailChangeSubmit()
    {
        if(ar == true)
        {
            return null;
        }

        new_email = $("input[name='login_email']").val();

        if(new_email == "")
        {
            emlchangemsg("<?php echo $this->translation->form_unfilled ?>");
            return null;
        }

        parameters = { new_email: new_email }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('profile', 'email') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function () { sp(true); emlchangemsg(""); },
            complete: function ()   { sp(false); setEmailChange(false); },
            success: function (xml) 
            { 
                data = $(xml).find('data');
                accepted = data.find('accepted').text();
                message = data.find('message').text();
                emlchangemsg(message);
            }, 
            error: function () { err(); }
        });
    }

    /* TRIGGERS */

    /* edit */

    $("input[name='editsubmit']").click(function() 
    {
        editSubmit();
    });

    /* password change */

    $("#pwdchangelnk").click(function() 
    {
        setPasswordChange(true);
    });

    $("input[name='pwdchangesubmit']").click(function() 
    {
        passwordChangeSubmit();
    });

    $("input[name='pwdchangecancel']").click(function() 
    {
        setPasswordChange(false);
    });

    /* email change */

    $("#emlchangelnk").click(function() 
    {
        setEmailChange(true);
    });

    $("input[name='emlchangesubmit']").click(function() 
    {
        emailChangeSubmit();
    });

    $("input[name='emlchangecancel']").click(function() 
    {
        setEmailChange(false);
    });
});
