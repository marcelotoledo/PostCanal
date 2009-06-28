var mytpl = null;

function toggle_form()
{
    mytpl.loginform.toggle();
    mytpl.recoveryform.toggle();
    mytpl.recovery = mytpl.recovery ^ true;

    if(mytpl.recovery==true)
    {
        mytpl.recoveryemail.val(mytpl.loginemail.val());
        mytpl.recoveryemail.focus();
    }
    else
    {
        mytpl.loginemail.val(mytpl.recoveryemail.val());
        mytpl.loginemail.focus();
    }

    login_msg('');
    recovery_msg('');
}

function recovery_msg(m)
{
    mytpl.recoverymsg.text(m);
}

function recovery_submit_cb(d)
{
    if(d.length==0) { server_error(); return false; }
    recovery_msg(d.find('message').text());
}

function recovery_submit()
{
    if(mytpl.recoveryemail.val()=="")
    {
        recovery_msg("<?php echo $this->translation()->enter_an_email ?>");
        return false;
    }

    $.ajax
    ({
        type: "POST",
        url: "/profile/recovery",
        dataType: "xml",
        data: { email: mytpl.recoveryemail.val() },
        beforeSend: function() { set_active_request(true); },
        complete: function() { set_active_request(false); },
        success: function (xml) { recovery_submit_cb($(xml).find('data')); },
        error: function () { server_error(); }
    });
}

function login_msg(m)
{
    mytpl.loginmsg.text(m);
}

function login_submit_cb(d)
{
    if(d.length==0) { server_error(); return null; }
    if(d.find('login').text()=="true") 
    {
        window.location="./dashboard";
    }
    login_msg(d.find('message').text());
}

function login_submit()
{
    if(mytpl.loginemail.val() == "" || mytpl.loginpassword.val() == "")
    {
        login_msg("<?php echo $this->translation()->form_incomplete ?>");
        return false;
    }

    $.ajax
    ({
        type: "post",
        url: "/profile/login",
        datatype: "xml",
        data: { email    : mytpl.loginemail.val(), 
                password : mytpl.loginpassword.val() },
        beforeSend: function () { set_active_request(true); },
        complete: function() { set_active_request(false); },
        success: function (xml) { login_submit_cb($(xml).find('data')); },
        error: function () { server_error(); } 
    });
}

$(document).ready(function()
{
    /* template vars */

    mytpl = 
    {
        recovery         : false,

        loginform        : $("#loginform"),
        loginemail       : $("#loginform").find("input[name='email']"),
        loginpassword    : $("#loginform").find("input[name='password']"),
        loginmsg         : $("#loginform").find("div.inputmessage"),
        loginsubmit      : $("#loginsubmit"),

        recoveryform     : $("#recoveryform"),
        recoveryemail    : $("#recoveryform").find("input[name='email']"),
        recoverypassword : $("#recoveryform").find("input[name='password']"),
        recoverymsg      : $("#recoveryform").find("div.inputmessage"),
        recoverysubmit   : $("#recoverysubmit"),

        pwdlnk           : $("#pwdlnk"),
        siglnk           : $("#siglnk"),
        signinsubmit     : $("#signinsubmit"),
        retrievesubmit   : $("#retrievesubmit"),
        msgcontainer     : $("#message")
    };

    /* triggers */

    mytpl.pwdlnk.click(function()
    {
        if(active_request==false) { toggle_form(); }
        return false;
    });

    mytpl.siglnk.click(function()
    {
        if(active_request==false) { toggle_form(); }
        return false;
    });

    mytpl.loginsubmit.click(function() 
    {
        if(active_request==false) { login_submit(); }
    });

    mytpl.recoverysubmit.click(function() 
    {
        if(active_request==false) { recovery_submit(); }
    });

    mytpl.loginform.find("input[name='password']").keypress(function(e) 
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            mytpl.signinsubmit.click();
        }
    });

    mytpl.loginform.find("input[name='email']").focus();
});
