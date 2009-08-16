var mytpl = null;


function signin_msg(m)
{
    alert(m);
}

function signin_submit_cb(d)
{
    if(d.length==0) { server_error(); return null; }
    if(d.find('login').text()=="true") 
    {
        window.location="./reader";
    }
    else
    {
        signin_msg(d.find('message').text());
    }
}

function signin_submit()
{
    var _data = { email    : mytpl.inputemail.val() ,
                  password : mytpl.inputpassword.val() }

    if(_data.email == "" || _data.password == "")
    {
        signin_msg("<?php echo $this->translation()->form_incomplete ?>");
        return false;
    }

    do_request('POST', './profile/login', _data, signin_submit_cb);
}


$(document).ready(function()
{
    /* template vars */

    mytpl = 
    {
        inputemail    : $("#input-email"),
        inputpassword : $("#input-password"),
        linkforgot    : $("#link-forgot"),
        buttonsignin  : $("#button-signin")
    };

    /* triggers */

    mytpl.linkforgot.click(function()
    {
        if(active_request==false) {  }
        $(this).blur();
        return false;
    });

    mytpl.buttonsignin.click(function()
    {
        if(active_request==false) { signin_submit(); }
        $(this).blur();
        return false;
    });

    mytpl.inputpassword.keypress(function(e) 
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            mytpl.buttonsignin.click();
        }
    });

    mytpl.inputemail.keypress(function(e) 
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
        }
    });

    mytpl.inputemail.focus();
});
