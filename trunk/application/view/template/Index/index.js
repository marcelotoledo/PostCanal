var mytpl = null;

function toggle_form()
{
    mytpl.recoverysent.hide();
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

function reset_form()
{
    mytpl.recoverysent.hide();
    mytpl.loginform.show();
    mytpl.recoveryform.hide();
    mytpl.recovery = false;
    mytpl.loginemail.val(mytpl.recoveryemail.val());
    mytpl.loginemail.focus();
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

    mytpl.recoveryform.hide();
    mytpl.recoverysent.show();
}

function recovery_submit()
{
    var _eml = null;

    if((_eml = mytpl.recoveryemail.val())=="")
    {
        recovery_msg("<?php echo $this->translation()->enter_an_email ?>");
        return false;
    }

    do_request('POST', './profile/recovery', { email: _eml }, recovery_submit_cb);
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
        window.location="./reader";
    }
    login_msg(d.find('message').text());
}

function login_submit()
{
    var _ld = { email    : mytpl.loginemail.val() ,
                password : mytpl.loginpassword.val() }

    if(_ld.email == "" || _ld.password == "")
    {
        login_msg("<?php echo $this->translation()->form_incomplete ?>");
        return false;
    }

    do_request('POST', './profile/login', _ld, login_submit_cb);
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

        recoverysent     : $("#recoverysent"),

        pwdlnk           : $("#pwdlnk"),
        siglnk           : $("#siglnk"),
        siglnk2          : $("#siglnk2"),
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

    mytpl.siglnk2.click(function()
    {
        if(active_request==false) { reset_form(); }
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
            mytpl.loginsubmit.click();
        }
    });

    mytpl.recoveryform.find("input[name='email']").keypress(function(e) 
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            mytpl.recoverysubmit.click();
        }
    });

    mytpl.loginform.find("input[name='email']").focus();
});
