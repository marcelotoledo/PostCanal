var my_template = null;

function register_msg(m)
{
    my_template.register_msg.text(m).show();
}

function register_submit_cb(d)
{
    if(d.length==0) { server_error(); return null; }
    if(d.find('register').text()=="true") 
    {
        window.location="/signup/welcome";
    }
    else
    {
        //register_msg(d.find('message').text());
        alert(d.find('message').text());
        window.location="/signup";
    }
}

function selected_country()
{
    return my_template.input_country.find("option:selected").val();
}

function selected_timezone()
{
    return my_template.input_timezone.find("option:selected").val();
}

function register_submit()
{
    var _data = { email     : my_template.input_email.val() ,
                  password  : my_template.input_password.val(),
                  passwordc : my_template.input_confirm.val(),
                  name      : my_template.input_name.val(), 
                  country   : selected_country(), 
                  timezone  : selected_timezone() };

    if(_data.email     == "" ||
       _data.password  == "" ||
       _data.passwordc == "" ||
       _data.name      == "" ||
       _data.country   == "")
    {
        register_msg("Please fill up the form correctly.");
        return false;
    }

    if(_data.password != _data.passwordc)
    {
        register_msg("Password and confirmation does not match.");
        return false;
    }

    if(_data.password.length < 6 || _data.passwordc.length > 32)
    {
        register_msg("Invalid password length. Please use 6 to 32 characters.");
        return false;
    }

    $(window).scrollTop(0);
    do_request('POST', '/profile/register', _data, register_submit_cb);
}


$(document).ready(function()
{
    /* template vars */

    my_template = 
    {
        input_email     : $("#input-email"),
        input_password  : $("#input-password"),
        input_confirm   : $("#input-confirm"),
        input_name      : $("#input-name"),
        input_country   : $("#input-country"),
        input_timezone  : $("#input-timezone"),
        submit_register : $("#submit-register"),
        register_msg    : $("#register-msg")
    };

    /* triggers */

    my_template.submit_register.click(function()
    {
        if(active_request==false) { register_submit(); }
        $(this).blur();
        return false;
    });

    function load_timezone_cb(d)
    {
        var _tz = d.find('timezone').children() ,
            _i   = 0 ,
            _j   = "",
            _l   = Array(),
            _s   = "";

        for(;_i<_tz.length;_i++)
        {
            _j = _tz[_i].firstChild.nodeValue;
            _l[_i] = "<option value=\"" + _j + "\"" + _s + ">" + _j.replace('_',' ') + "</option>";
        }

        my_template.input_timezone.html(_l.join("\n"));
        my_template.input_timezone.attr('disabled', false);
    }

    function load_timezone()
    {
        var _data = { territory : selected_country() };
        my_template.input_timezone.attr('disabled', true);
        do_request('POST', '/profile/timezone', _data , load_timezone_cb);
    }

    my_template.input_country.change(function()
    {
        load_timezone();
        $(this).blur();
    });

    my_template.input_timezone.change(function()
    {
        $(this).blur();
    });

    my_template.input_name.focus();
    my_template.input_password.focus(function() { $('body').scrollTop(200); });
    my_template.input_confirm.focus(function() { $('body').scrollTop(300); });

    /* invitation (temporary) */

    if(my_template.input_email.val()=='')
    {
        my_template.input_name.blur();
        $("#invitationlnk").click();
    }
});
