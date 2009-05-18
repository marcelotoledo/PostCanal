$(document).ready(function()
{
    /* DEFAULTS */

    var ar = false; /* active request */
    var rg = false; /* register on/off */

    /* spinner */

    $.b_spinner
    ({
        image: "<?php B_Helper::img_src('spinner.gif') ?>", 
        message: "... <?php echo $this->translation()->application_loading ?>"
    });

    /* SWITCHES */

    /* spinner */

    function sp(b)
    {
        ((ar = b) == true) ? $.b_spinner_start() : $.b_spinner_stop();
    }

    /* toggle form */

    function tf()
    {
        $("#ftitlog").toggle();
        $("#ftitreg").toggle();
        $("#lnkrow").toggle();
        $("#confirmrow").toggle();
        $("input[name='regcancel']").toggle();
        $("#message").hide();
        rg = rg ^ true;
    }

    /* message */

    function msg(m)
    {
        if(m=="")
        {
            $("#message td").html("");
            $("#message").hide();
        }
        else
        {
            $("#message td").html(m);
            $("#message").show();
        }
    }

    /* ACTIONS */

    /* error */

    function err()
    {
        alert("<?php echo $this->translation()->server_error ?>");
    }

    /* password recovery */

    function recovery()
    {
        if($("input[name='email']").val() == "")
        {
            msg("<?php echo $this->translation()->recovery_email ?>");
            return null;
        }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('profile', 'recovery') ?>",
            dataType: "xml",
            data: { email: $("input[name='email']").val() },
            beforeSend: function()
            {
                sp(true);
            },
            complete: function()
            {
                sp(false);
            },
            success: function (xml) 
            { 
                d = $(xml).find('data');
                m = d.find('message').text();
                msg(m);
            }, 
            error: function () { err(); } 
        });
    }

    /* form submit */

    function fs()
    {
        if(rg == true) { register(); } else { login(); }
    }

    /* login register */

    function register()
    {
        email = $("input[name='email']").val();
        password = $("input[name='password']").val();
        confirm_ = $("input[name='confirm']").val();

        if(email == "" || password == "" || confirm_ == "")
        {
            msg("<?php echo $this->translation()->form_incomplete ?>");
            return null;
        }

        if(password != confirm_)
        {
            msg("<?php echo $this->translation()->form_not_match ?>");
            return null;
        }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('profile', 'register') ?>",
            dataType: "xml",
            data: { email: email, password: password, confirm: confirm_ },
            beforeSend: function ()
            {
                sp(true);
            },
            complete: function()
            {
                sp(false);
            },
            success: function (xml) 
            { 
                d = $(xml).find('data');
                r = d.find('register').text();
                m = d.find('message').text();
                if(r == "true") tf();
                msg(m);
            }, 
            error: function () { err(); } 
        });
    }

    /* login authentication */

    function login()
    {
        email = $("input[name='email']").val();
        password = $("input[name='password']").val();

        if(email == "" || password == "")
        {
            msg("<?php echo $this->translation()->form_incomplete ?>");
            return null;
        }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('profile', 'login') ?>",
            dataType: "xml",
            data: { email: email, password: password },
            beforeSend: function ()
            {
                sp(true);
            },
            complete: function()
            {
                sp(false);
            },
            success: function (xml) 
            { 
                d = $(xml).find('data');
                l = d.find('login').text();
                m = d.find('message').text();
                u = "<?php B_Helper::url('dashboard') ?>";
                if(l == "true") window.location = u;
                msg(m);
            }, 
            error: function () { err(); } 
        });
    }

    /* TRIGGERS */

    $("#reglnk").click(function()
    {
        if(ar == false) { tf(); }
    });

    $("input[name='regcancel']").click(function() 
    {
        if(ar == false) { tf(); }
    });

    $("#pwdlnk").click(function()
    {
        if(ar == false) { recovery(); }
    });

    $("input[name='frmsubmit']").click(function() 
    {
        if(ar == false) { fs(); }
    });

    $("input[name='password']").keypress(function(e) 
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            $("input[name='frmsubmit']").click();
        }
    });

    $("input[name='confirm']").keypress(function(e) 
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            $("input[name='frmsubmit']").click();
        }
    });

    $("input[name='email']").focus();
});
