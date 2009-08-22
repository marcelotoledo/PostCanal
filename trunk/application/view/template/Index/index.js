var mytpl = null;


function login_msg(m)
{
    mytpl.loginmsg.text(m).show();
}

function login_submit_cb(d)
{
    if(d.length==0) { server_error(); return null; }
    if(d.find('login').text()=="true") 
    {
        window.location="./reader";
    }
    else
    {
        login_msg(d.find('message').text());
    }
}

function login_submit()
{
    var _data = { email    : mytpl.loginemail.val() ,
                  password : mytpl.loginpassword.val() }

    if(_data.email == "" || _data.password == "")
    {
        login_msg("<?php echo $this->translation()->form_incomplete ?>");
        return false;
    }

    do_request('POST', './profile/login', _data, login_submit_cb);
}

function login_show()
{
    mytpl.loginttl.show();
    mytpl.recoveryttl.hide();
    mytpl.loginform.show();
    mytpl.recoveryform.hide();
    mytpl.retrievedttl.hide();
    mytpl.retrievednotice.hide();
}

function recovery_toggle()
{
    mytpl.loginttl.toggle();
    mytpl.recoveryttl.toggle();
    mytpl.loginform.toggle();
    mytpl.recoveryform.toggle();
}

function recovery_msg(m)
{
    mytpl.recoverymsg.text(m).show();
}

function recovery_submit_cb(d)
{
    if(d.length==0) { server_error(); return false; }
    mytpl.loginttl.hide();
    mytpl.recoveryttl.hide();
    mytpl.loginform.hide();
    mytpl.recoveryform.hide();
    mytpl.retrievedttl.show();
    mytpl.retrievednotice.show();
}

function recovery_submit()
{
    var _data = null;

    if((_data = mytpl.recoveryemail.val())=="")
    {
        recovery_msg("<?php echo $this->translation()->enter_an_email ?>");
        return false;
    }

    do_request('POST', './profile/recovery', { email: _data }, recovery_submit_cb);
}


$(document).ready(function()
{
    /* template vars */

    mytpl = 
    {
        linkrecovery    : $("#link-recovery"),
        linkremembered  : $("#link-remembered"),
        linklogin       : $("#link-login"),
        linkretrieved   : $("#link-retrieved"),
        submitlogin     : $("#submit-login"),
        submitrecovery  : $("#submit-recovery"),
        loginttl        : $("#login-ttl"),
        loginform       : $("#login-form"),
        loginemail      : $("#login-email"),
        loginpassword   : $("#login-password"),
        loginmsg        : $("#login-msg"),
        recoveryttl     : $("#recovery-ttl"),
        recoveryform    : $("#recovery-form"),
        recoveryemail   : $("#recovery-email"),
        recoverymsg     : $("#recovery-msg"),
        retrievedttl    : $("#retrieved-ttl"),
        retrievednotice : $("#retrieved-notice")
    };

    /* triggers */

    mytpl.linkrecovery.click(function()
    {
        if(active_request==false) { recovery_toggle(); }
        $(this).blur();
        mytpl.recoveryemail.focus();
        return false;
    });

    mytpl.linkremembered.click(function()
    {
        if(active_request==false) { recovery_toggle(); }
        $(this).blur();
        return false;
    });

    mytpl.linkretrieved.click(function()
    {
        if(active_request==false) { login_show(); }
        $(this).blur();
        return false;
    });

    mytpl.linklogin.click(function()
    {
        recovery_toggle();
        $(this).blur();
        return false;
    });

    mytpl.submitlogin.click(function()
    {
        if(active_request==false) { login_submit(); }
        $(this).blur();
        return false;
    });

    mytpl.submitrecovery.click(function()
    {
        if(active_request==false) { recovery_submit(); }
        $(this).blur();
        return false;
    });

    mytpl.loginpassword.keypress(function(e) 
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            mytpl.submitlogin.click();
        }
    });

    mytpl.loginemail.focus();
});
