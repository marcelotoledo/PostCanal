var mytpl = null;


function form_message(m)
{
    (m=="") ? 
        mytpl.msgcontainer.hide().find("td").html("") :
        mytpl.msgcontainer.show().find("td").html(m) ;
}

function email_change()
{
    if(mytpl.password.val() == "")
    {
        form_message("<?php echo $this->translation()->form_incomplete ?>");
        return null;
    }

    $.ajax
    ({
        type: "POST",
        url: "/profile/email",
        dataType: "xml",
        data: { email    : mytpl.email.val(), 
                password : mytpl.password.val(),
                user     : mytpl.user.val() },
        beforeSend: function() { set_active_request(true);  },
        complete: function()   { set_active_request(false); },
        success: function (xml) 
        { 
            var _data = $(xml).find('data');
            if(_data.length==0) { server_error(); return null; }

            if(_data.find('accepted').text()=="true") 
            {
                mytpl.emlform.toggle();
                mytpl.changenotice.toggle();
            }
            else
            {
                form_message(_data.find('message').text());
            }
        }, 
        error: function (data) { error_message(); }
    });
}


$(document).ready(function()
{
    mytpl =
    {
        email           : $("#email"),
        password        : $("#password"),
        user            : $("#user"),
        emlform         : $("#emlform"),
        changenotice    : $("#changenotice"),
        emlchangesubmit : $("#emlchangesubmit"),
        msgcontainer    : $("#message")
    };

    /* triggers */

    mytpl.password.keypress(function(e) 
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            mytpl.emlchangesubmit.click();
        }
    });

    mytpl.emlchangesubmit.click(function() 
    {
        if(active_request==false) {  email_change(); }
    });
});
