var mytpl = null;


function form_message(m)
{
    (m=="") ? 
        mytpl.msgcontainer.hide().find("td").html("") :
        mytpl.msgcontainer.show().find("td").html(m) ;
}

function email_change_callback(d)
{
    if(d.length==0) { server_error(); return false; }

    if(d.find('accepted').text()=="true") 
    {
        mytpl.emlform.toggle();
        mytpl.changenotice.toggle();
    }
    else
    {
        form_message(d.find('message').text());
    }
}

function email_change()
{
    var _data: { email    : mytpl.email.val(), 
                 password : mytpl.password.val(),
                 user     : mytpl.user.val() };

    if(_data.password=="")
    {
        form_message("<?php echo $this->translation()->form_incomplete ?>");
        return false;
    }

    do_request('POST', './profile/email', _data, email_change_callback);
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
