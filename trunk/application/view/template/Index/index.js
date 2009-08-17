var mytpl = null;


function login_msg(m)
{
    alert(m);
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

function recovery_toggle()
{
    mytpl.signinttl.toggle();
    mytpl.forgotttl.toggle();
    mytpl.signinform.toggle();
    mytpl.forgotform.toggle();
}

function recovery_msg(m)
{
    alert(m);
}

function recovery_submit_cb(d)
{
    if(d.length==0) { server_error(); return false; }
}

function recovery_submit()
{
    var _data = null;

    if((_data = mytpl.loginemail.val())=="")
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
        loginemail     : $("#login-email"),
        forgotemail    : $("#forgot-email"),
        loginpassword  : $("#login-password"),
        linkforgot     : $("#link-forgot"),
        linkremembered : $("#link-remembered"),
        linksignin     : $("#link-signin"),
        submitlogin    : $("#submit-login"),
        submitretrieve : $("#submit-retrieve"),
        signinttl      : $("#signin-ttl"),
        signinform     : $("#signin-form"),
        forgotttl      : $("#forgot-ttl"),
        forgotform     : $("#forgot-form"),
        recoverymsg    : $("#recoverymsg")
    };

    /* triggers */

    mytpl.linkforgot.click(function()
    {
        if(active_request==false) { recovery_toggle(); }
        $(this).blur();
        mytpl.forgotemail.focus();
        return false;
    });

    mytpl.linkremembered.click(function()
    {
        if(active_request==false) { recovery_toggle(); }
        $(this).blur();
        return false;
    });

    mytpl.linksignin.click(function()
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

    mytpl.loginpassword.keypress(function(e) 
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            mytpl.submitlogin.click();
        }
    });

    mytpl.loginemail.focus();
});
