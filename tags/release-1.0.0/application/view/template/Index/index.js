var my_template = null;


function login_msg(m)
{
    my_template.login_msg.text(m).show();
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
    var _data = { email    : my_template.login_email.val() ,
                  password : my_template.login_password.val() }

    if(_data.email == "" || _data.password == "")
    {
        login_msg("<?php echo $this->translation()->form_incomplete ?>");
        return false;
    }

    do_request('POST', './profile/login', _data, login_submit_cb);
}

function login_show()
{
    my_template.login_ttl.show();
    my_template.recovery_ttl.hide();
    my_template.login_container.show();
    my_template.recovery_form.hide();
    my_template.retrieved_ttl.hide();
    my_template.retrieved_notice.hide();
}

function recovery_toggle()
{
    my_template.login_ttl.toggle();
    my_template.recovery_ttl.toggle();
    my_template.login_container.toggle();
    my_template.recovery_form.toggle();
}

function recovery_msg(m)
{
    my_template.recovery_msg.text(m).show();
}

function recovery_submit_cb(d)
{
    if(d.length==0) { server_error(); return false; }
    my_template.login_ttl.hide();
    my_template.recovery_ttl.hide();
    my_template.login_container.hide();
    my_template.recovery_form.hide();
    my_template.retrieved_ttl.show();
    my_template.retrieved_notice.show();
}

function recovery_submit()
{
    var _data = null;

    if((_data = my_template.recovery_email.val())=="")
    {
        recovery_msg("<?php echo $this->translation()->enter_an_email ?>");
        return false;
    }

    do_request('POST', './profile/recovery', { email: _data }, recovery_submit_cb);
}


$(document).ready(function()
{
    /* template vars */

    my_template = 
    {
        link_recovery    : $("#link-recovery"),
        link_remembered  : $("#link-remembered"),
        link_login       : $("#link-login"),
        link_retrieved   : $("#link-retrieved"),
        submit_login     : $("#submit-login"),
        submit_recovery  : $("#submit-recovery"),
        login_ttl        : $("#login-ttl"),
        login_container  : $("#login-fc"),
        login_form       : $("#login-form"),
        login_email      : $("#login-email"),
        login_password   : $("#login-password"),
        login_msg        : $("#login-msg"),
        recovery_ttl     : $("#recovery-ttl"),
        recovery_form    : $("#recovery-form"),
        recovery_email   : $("#recovery-email"),
        recovery_msg     : $("#recovery-msg"),
        retrieved_ttl    : $("#retrieved-ttl"),
        retrieved_notice : $("#retrieved-notice")
    };

    /* triggers */

    my_template.login_form.submit(function()
    {
        if(active_request==false) { login_submit(); }
        $(this).blur();
        return null;
    });

    my_template.link_recovery.click(function()
    {
        if(active_request==false) { recovery_toggle(); }
        $(this).blur();
        my_template.recovery_email.focus();
        return false;
    });

    my_template.link_remembered.click(function()
    {
        if(active_request==false) { recovery_toggle(); }
        $(this).blur();
        return false;
    });

    my_template.link_retrieved.click(function()
    {
        if(active_request==false) { login_show(); }
        $(this).blur();
        return false;
    });

    my_template.link_login.click(function()
    {
        recovery_toggle();
        $(this).blur();
        return false;
    });

    my_template.submit_recovery.click(function()
    {
        if(active_request==false) { recovery_submit(); }
        $(this).blur();
        return false;
    });

    my_template.login_password.keypress(function(e) 
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            my_template.login_form.submit();
        }
    });

    my_template.login_email.focus();
});
