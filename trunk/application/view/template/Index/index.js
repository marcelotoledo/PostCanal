var mytpl = null;


function toggle_register()
{
    mytpl.ftitlog.toggle();
    mytpl.ftitreg.toggle();
    mytpl.lnkrow.toggle();
    mytpl.confirmrow.toggle();
    mytpl.regcancel.toggle();
    mytpl.msgcontainer.hide();
    mytpl.register = mytpl.register ^ true;
}

function form_message(m)
{
    (m=="") ? 
        mytpl.msgcontainer.hide().find("td").html("") :
        mytpl.msgcontainer.show().find("td").html(m) ;
}

function error_message()
{
    alert("<?php echo $this->translation()->server_error ?>");
}

function password_recovery()
{
    if(mytpl.email.val()=="")
    {
        form_message("<?php echo $this->translation()->enter_an_email ?>");
        return null;
    }

    $.ajax
    ({
        type: "POST",
        url: "<?php B_Helper::url('profile', 'recovery') ?>",
        dataType: "xml",
        data: { email: mytpl.email.val() },
        beforeSend: function() { set_active_request(true); },
        complete: function() { set_active_request(false); },
        success: function (xml) 
        { 
            var _data = $(xml).find('data');
            form_message(_data.find('message').text());
        }, 
        error: function () { error_message(); } 
    });
}

function form_submit()
{
    (mytpl.register==true) ? register_submit() : login_submit();
}

function register_submit()
{
    if(mytpl.email.val()     == "" || 
       mytpl.password.val()  == "" || 
       mytpl.passwordc.val() == "")
    {
        form_message("<?php echo $this->translation()->form_incomplete ?>");
        return null;
    }

    if(mytpl.password.val() != mytpl.passwordc.val())
    {
        form_message("<?php echo $this->translation()->password_not_match ?>");
        return null;
    }

    $.ajax
    ({
        type: "post",
        url: "<?php b_helper::url('profile', 'register') ?>",
        datatype: "xml",
        data: { email     : mytpl.email.val(), 
                password  : mytpl.password.val(), 
                passwordc : mytpl.passwordc.val() },
        beforesend: function () { set_active_request(true); },
        complete: function() { set_active_request(false); },
        success: function (xml) 
        { 
            var _data = $(xml).find('data');
            if(_data.find('register').text()=="true") { toggle_register(); }
            form_message(_data.find('message').text());
        }, 
        error: function () { error_message(); } 
    });
}

function login_submit()
{
    if(mytpl.email.val()    == "" || 
       mytpl.password.val() == "")
    {
        form_message("<?php echo $this->translation()->form_incomplete ?>");
        return null;
    }

    $.ajax
    ({
        type: "post",
        url: "<?php b_helper::url('profile', 'login') ?>",
        datatype: "xml",
        data: { email    : mytpl.email.val(), 
                password : mytpl.password.val() },
        beforesend: function () { set_active_request(true); },
        complete: function() { set_active_request(false); },
        success: function (xml) 
        { 
            var _data = $(xml).find('data');
            if(_data.find('login').text()=="true") { window.location="<?php B_Helper::url('dashboard') ?>"; }
            form_message(_data.find('message').text());
        }, 
        error: function () { error_message(); } 
    });
}


$(document).ready(function()
{
    /* template vars */

    mytpl = 
    {
        register     : false,

        ftitlog      : $("#ftitlog"),
        ftitreg      : $("#ftitreg"),
        email        : $("#email"),
        password     : $("#password"),
        passwordc    : $("#passwordc"),
        lnkrow       : $("#lnkrow"),
        confirmrow   : $("#confirmrow"),
        regcancel    : $("#regcancel"),
        frmsubmit    : $("#frmsubmit"),
        msgcontainer : $("#message"),
        reglnk       : $("#reglnk"),
        pwdlnk       : $("#pwdlnk")
    };

    /* triggers */

    mytpl.reglnk.click(function()
    {
        if(active_request==false) { toggle_register(); }
        return false;
    });
   
    mytpl.regcancel.click(function() 
    {
        if(active_request==false) { toggle_register(); }
    });

    mytpl.pwdlnk.click(function()
    {
        if(active_request==false) { password_recovery(); }
        return false;
    });

    mytpl.frmsubmit.click(function() 
    {
        if(active_request==false) { form_submit(); }
    });

    mytpl.password.keypress(function(e) 
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            mytpl.frmsubmit.click();
        }
    });

    mytpl.passwordc.keypress(function(e) 
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            mytpl.frmsubmit.click();
        }
    });

    mytpl.email.focus();
});
