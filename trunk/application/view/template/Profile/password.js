var mytpl = null;


function form_message(m)
{
    (m=="") ? 
        mytpl.msgcontainer.hide().html("") :
        mytpl.msgcontainer.show().html(m) ;
}

function password_recovery_callback(d)
{
    if(d.length==0) { server_error(); return false; }

    if(d.find('updated').text()=="true") 
    {
        mytpl.pwdform.toggle();
        mytpl.changenotice.toggle();
    }
    else
    {
        form_message(d.find('message').text());
    }
}

function password_recovery()
{
    var _data = { email     : mytpl.email.val(), 
                  user      : mytpl.user.val(),
                  password  : mytpl.password.val(), 
                  passwordc : mytpl.passwordc.val() };

    if(_data.password=="" || _data.passwordc=="")
    {
        form_message("Please fill up the form correctly.");
        return false;
    }

    if(_data.password != _data.passwordc)
    {
        form_message("Password and confirmation does not match.");
        return false;
    }

    do_request('POST', './profile/password', _data, password_recovery_callback);
};


$(document).ready(function()
{
    /* template vars */

    mytpl = 
    {
        email        : $("#email"),
        user         : $("#user"),
        password     : $("#password"),
        passwordc    : $("#passwordc"),
        pwdcsubmit   : $("#pwdchangesubmit"),
        msgcontainer : $("#pwdchange-msg"),
        pwdform      : $("#pwdform"),
        changenotice : $("#changenotice")
    };

    /* triggers */

    mytpl.pwdcsubmit.click(function()
    {
        if(active_request==false) { password_recovery(); }
    });

    mytpl.passwordc.keypress(function(e) 
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            mytpl.pwdcsubmit.click();
        }
    });

    mytpl.password.focus();
});
