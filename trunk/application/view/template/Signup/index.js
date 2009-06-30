var mytpl = null;

function timezone_populate(d)
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

    mytpl.inputtz.html(_l.join("\n"));
    mytpl.inputtz.attr('disabled', false);
}

function signup_msg(m)
{
    mytpl.formmsg.text(m);
}

function signup_submit()
{
    if(mytpl.inputemail.val()   == "" || 
       mytpl.inputpasswd.val()  == "" || 
       mytpl.inputpasswdc.val() == "" || 
       mytpl.inputname.val()    == "" ||
       selected_country()       == "")
    {
        signup_msg("<?php echo $this->translation()->form_incomplete ?>");
        return null;
    }

    if(mytpl.inputpasswd.val() != mytpl.inputpasswdc.val())
    {
        signup_msg("<?php echo $this->translation()->password_not_match ?>");
        return null;
    }

    if(mytpl.inputpasswd.val().length < 6 ||
       mytpl.inputpasswd.val().length > 32)
    {
        signup_msg("<?php echo $this->translation()->password_length_invalid ?>");
        return null;
    }

    $.ajax
    ({
        type: "post",
        url: "./profile/register",
        datatype: "xml",
        data: { email     : mytpl.inputemail.val(), 
                password  : mytpl.inputpasswd.val(), 
                passwordc : mytpl.inputpasswdc.val(),
                name      : mytpl.inputname.val(),
                country   : selected_country(), 
                timezone  : selected_tz() },
        beforeSend: function () { set_active_request(true); },
        complete: function() { set_active_request(false); },
        success: function (xml) 
        { 
            var _data = $(xml).find('data');
            if(_data.length==0) { server_error(); return null; }
            if(_data.find('register').text()=="true") { window.location='./signup/welcome'; }
            signup_msg(_data.find('message').text());
        }, 
        error: function () { server_error(); } 
    });
}

function selected_country()
{
    return mytpl.inputcountry.find("option:selected").val();
}

function selected_tz()
{
    return mytpl.inputtz.find("option:selected").val();
}

$(document).ready(function()
{
    /* template vars */

    mytpl = 
    {
        signupform   : $("#signupform"),
        inputemail   : $("input[name='email']"),
        inputpasswd  : $("input[name='password']"),
        inputpasswdc : $("input[name='passwordc']"),
        inputname    : $("input[name='name']"),
        inputcountry : $("select[name='country']"),
        inputtz      : $("select[name='timezone']"),
        signupbtn    : $("#signup_button"),
        formmsg      : $("#formmessage")
    };

    function load_timezone()
    {
        $.ajax
        ({
            type: "GET",
            url: "./profile/timezone",
            dataType: "xml",
            data: { territory: selected_country() },
            beforeSend: function () { mytpl.inputtz.attr('disabled', true); 
                                      set_active_request(true); },
            complete: function ()   { mytpl.inputtz.attr('disabled', false);
                                      set_active_request(false); },
            success: function (xml) 
            { 
                timezone_populate($(xml).find('data'));
            }, 
            error: function () { server_error(); }
        });
    }
    
    load_timezone();

    mytpl.inputcountry.change(function()
    {
        load_timezone();
        $(this).blur();
    });

    mytpl.inputtz.change(function()
    {
        $(this).blur();
    });

    mytpl.signupbtn.click(function() 
    {
        if(active_request==false) { signup_submit(); }
    });

    mytpl.inputemail.focus();
});
