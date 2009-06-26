var mytpl = null;


function form_message(m)
{
    (m=="") ? 
        mytpl.msgcontainer.hide().find("td").html("") :
        mytpl.msgcontainer.show().find("td").html(m) ;
}

function password_recovery()
{
    if(mytpl.password.val() == "" || 
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
        type: "POST",
        url: "/profile/password",
        dataType: "xml",
        data: { email     : mytpl.email.val(), 
                user      : mytpl.user.val(),
                password  : mytpl.password.val(), 
                passwordc : mytpl.passwordc.val() },
        beforeSend: function () { set_active_request(true); },
        complete: function() { set_active_request(false); },
        success: function (xml) 
        { 
            var _data = $(xml).find('data');
            if(_data.length==0) { server_error(); return null; }

            if(_data.find('updated').text()=="true") 
            {
                mytpl.pwdform.toggle();
                mytpl.changenotice.toggle();
            }
            else
            {
                form_message(_data.find('message').text());
            }
        }, 
        error: function (data) { server_error(); }
    });
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
        msgcontainer : $("#message"),
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
