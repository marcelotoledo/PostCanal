var mytpl = null;


function toggle_email_change(b)
{
    if(b)
    {
        mytpl.neweml.attr('disabled', false);
        mytpl.emlchangecancel.attr('disabled', false);
        mytpl.emlchangesubmit.attr('disabled', false);
        mytpl.neweml.focus();
    }
    else
    {
        mytpl.neweml.attr('disabled', true);
        mytpl.emlchangecancel.attr('disabled', true);
        mytpl.emlchangesubmit.attr('disabled', true);
    }
}

function toggle_password_change(b)
{
    if(b)
    {
        mytpl.currentpwd.attr('disabled', false);
        mytpl.newpwd.attr('disabled', false);
        mytpl.confirmpwd.attr('disabled', false);
        mytpl.pwdchangecancel.attr('disabled', false);
        mytpl.pwdchangesubmit.attr('disabled', false);
        mytpl.currentpwd.focus();
    }
    else
    {
        mytpl.currentpwd.val("");
        mytpl.currentpwd.attr('disabled', true);
        mytpl.newpwd.val("");
        mytpl.newpwd.attr('disabled', true);
        mytpl.confirmpwd.val("");
        mytpl.confirmpwd.attr('disabled', true);
        mytpl.pwdchangecancel.attr('disabled', true);
        mytpl.pwdchangesubmit.attr('disabled', true);
    }
}

function edit_message(m)
{
    (m=="") ?
        mytpl.editmessage.hide().find("td").html("") :
        mytpl.editmessage.show().find("td").html(m) ;
}

function edit_submit()
{
    $.ajax
    ({
        type: "POST",
        url: "<?php B_Helper::url('profile', 'edit') ?>",
        dataType: "xml",
        data: { name            : mytpl.name.val(),
                local_territory : mytpl.territory.val(),
                local_timezone  : mytpl.timezone.val(),
                local_culture   : mytpl.culture.val() },
        beforeSend: function () { set_active_request(true); edit_message("");  },
        complete: function ()   { set_active_request(false); },
        success: function (xml) 
        { 
            var _data = $(xml).find('data');
            if(_data.length==0) { server_error(); return null; }
            edit_message(_data.find('message').text());
        }, 
        error: function () { server_error(); }
    });
}

function pwdchange_message(m)
{
    (m=="") ?
        mytpl.pwdchangemessage.hide().find("td").html("") :
        mytpl.pwdchangemessage.show().find("td").html(m) ;
}

function pwdchange_submit()
{
    if((mytpl.currentpwd.val() == "" || 
        mytpl.newpwd.val()     == "" || 
        mytpl.confirmpwd.val() == ""))
    {
        pwdchange_message("<?php echo $this->translation()->form_incomplete ?>");
        return null;
    }

    if(mytpl.newpwd.val() != mytpl.confirmpwd.val())
    {
        pwdchange_message("<?php echo $this->translation()->password_not_match ?>");
        return null;
    }

    $.ajax
    ({
        type: "POST",
        url: "<?php B_Helper::url('profile', 'password') ?>",
        dataType: "xml",
        data: { current   : mytpl.currentpwd.val(),
                password  : mytpl.newpwd.val(), 
                passwordc : mytpl.confirmpwd.val() },
        beforeSend: function () { set_active_request(true); pwdchange_message(""); },
        complete: function ()   { set_active_request(false); },
        success: function (xml) 
        { 
            var _data = $(xml).find('data');
            if(_data.length==0) { server_error(); return null; }
            pwdchange_message(_data.find('message').text());
            if(_data.find('updated').text()=="true") { toggle_password_change(false); }
        }, 
        error: function () { server_error(); }
    });
}

function emlchange_message(m)
{
    (m=="") ?
        mytpl.emlchangemessage.hide().find("td").html("") :
        mytpl.emlchangemessage.show().find("td").html(m) ;
}

function emlchange_submit()
{
    if(mytpl.neweml.val()=="")
    {
        emlchange_message("<?php echo $this->translation()->form_incomplete ?>");
        return null;
    }

    $.ajax
    ({
        type: "POST",
        url: "<?php B_Helper::url('profile', 'email') ?>",
        dataType: "xml",
        data: { new_email: mytpl.neweml.val() },
        beforeSend: function () { set_active_request(true); emlchange_message(""); },
        complete: function ()   { set_active_request(false); toggle_email_change(false); },
        success: function (xml) 
        { 
            var _data = $(xml).find('data');
            if(_data.length==0) { server_error(); return null; }
            emlchange_message(_data.find('message').text());
        }, 
        error: function () { server_error(); }
    });
}

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
        _s = (_j=="<?php echo $this->profile->local_timezone ?>") ? " selected" : "";
        _l[_i] = "<option value=\"" + _j + "\"" + _s + ">" + _j.replace('_',' ') + "</option>";
    }

    mytpl.timezone.html(_l.join("\n"));
    mytpl.timezone.attr('disabled', false);
}


$(document).ready(function()
{
    mytpl =
    {
        neweml           : $("#neweml"),
        emlchangelnk     : $("#emlchangelnk"),
        emlchangesubmit  : $("#emlchangesubmit"),
        emlchangecancel  : $("#emlchangecancel"),
        emlchangemessage : $("#emlchangemessage"),
        pwdchangelnk     : $("#pwdchangelnk"),
        currentpwd       : $("#currentpwd"),
        newpwd           : $("#newpwd"),
        confirmpwd       : $("#confirmpwd"),
        pwdchangesubmit  : $("#pwdchangesubmit"),
        pwdchangecancel  : $("#pwdchangecancel"),
        pwdchangemessage : $("#pwdchangemessage"),
        name             : $("#name"),
        territory        : $("#local_territory"),
        timezone         : $("#local_timezone"),
        culture          : $("#local_culture"),
        editmessage      : $("#editmessage"),
        editsubmit       : $("#editsubmit")
    };

    function selected_territory()
    {
        return mytpl.territory.find("option:selected").val();
    }

    function load_timezone()
    {
        $.ajax
        ({
            type: "GET",
            url: "<?php B_Helper::url('profile', 'timezone') ?>",
            dataType: "xml",
            data: { territory: selected_territory() },
            beforeSend: function () { set_active_request(true); },
            complete: function ()   { set_active_request(false); },
            success: function (xml) 
            { 
                timezone_populate($(xml).find('data'));
            }, 
            error: function () { server_error(); }
        });
    }
    
    load_timezone();

    /* triggers */

    mytpl.editsubmit.click(function()
    {
        if(active_request==false) { edit_submit(); }
    });

    mytpl.pwdchangelnk.click(function()
    {
        if(active_request==false) { toggle_password_change(true); }
        return false;
    });

    mytpl.pwdchangesubmit.click(function()
    {
        if(active_request==false) { pwdchange_submit(); }
    });

    mytpl.pwdchangecancel.click(function() 
    {
        if(active_request==false) { toggle_password_change(false); }
    });

    mytpl.emlchangelnk.click(function()
    {
        if(active_request==false) { toggle_email_change(true); }
        return false;
    });

    mytpl.neweml.keypress(function(e) 
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            mytpl.emlchangesubmit.click();
        }
    });

    mytpl.emlchangesubmit.click(function()
    {
        if(active_request==false) { emlchange_submit(); }
    });

    mytpl.emlchangecancel.click(function() 
    {
        if(active_request==false) { toggle_email_change(false); }
    });

    mytpl.territory.change(function()
    {
        load_timezone();
    });
});
