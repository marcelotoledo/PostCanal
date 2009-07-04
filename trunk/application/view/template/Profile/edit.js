var mytpl = null;

function set_tab(i)
{
    mytpl.tabhead.find("div.tabitem").removeClass('tabitem-selected');
    mytpl.tabgroup.find("div.tabcontainer").hide();
    mytpl.tabhead.find("div.tabitem[related='" + i + "']").addClass('tabitem-selected');
    mytpl.tabgroup.find("#" + i).show();
}

function edit_message(m)
{
    mytpl.editmessage.text(m);
}

function edit_submit()
{
    $.ajax
    ({
        type: "POST",
        url: "/profile/edit",
        dataType: "xml",
        data: { name            : mytpl.name.val(),
                local_territory : mytpl.territory.val(),
                local_timezone  : mytpl.timezone.val() // ,
                /* local_culture   : mytpl.culture.val() */ },
        beforeSend: function () { set_active_request(true); edit_message("");  },
        complete: function ()   { set_active_request(false); },
        success: function (xml) 
        { 
            var _data = $(xml).find('data');
            if(_data.length==0) { server_error(); return null; }
            if(_data.find('saved').text()=="true") { flash_message("<?php echo $this->translation()->saved ?>"); } else { /* void */ }
            // flash_message(_data.find('message').text());
        }, 
        error: function () { server_error(); }
    });
}

function pwdchange_message(m)
{
    mytpl.pwdchangemessage.text(m);
}

function pwdchange_after()
{
    mytpl.currentpwd.val('');
    mytpl.newpwd.val('');
    mytpl.confirmpwd.val('');
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
        url: "/profile/password",
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
            if(_data.find('updated').text()=="true") { flash_message("<?php echo $this->translation()->saved ?>"); pwdchange_after(); } else { pwdchange_message(_data.find('message').text()); }
        }, 
        error: function () { server_error(); }
    });
}

function emlchange_message(m)
{
    mytpl.emlchangemessage.text(m);
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
        url: "/profile/email",
        dataType: "xml",
        data: { new_email: mytpl.neweml.val() },
        beforeSend: function () { set_active_request(true); emlchange_message(""); },
        complete: function ()   { set_active_request(false); },
        success: function (xml) 
        { 
            var _data = $(xml).find('data');
            if(_data.length==0) { server_error(); return null; }
            if(_data.find('accepted').text()=="true") { flash_message("<?php echo $this->translation()->saved ?><br><small><?php echo $this->translation()->check_your_inbox_to_validate ?></small>"); } else { emlchange_message(_data.find('message').text()); }
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
        tabhead          : $("#edittab"),
        tabgroup         : $("#edittabgroup"),
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
            url: "/profile/timezone",
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

    mytpl.pwdchangesubmit.click(function()
    {
        if(active_request==false) { pwdchange_submit(); }
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

    mytpl.territory.change(function()
    {
        load_timezone();
        $(this).blur();
    });

    mytpl.timezone.change(function()
    {
        $(this).blur();
    });

    mytpl.tabhead.find("div.tabitem").click(function()
    {
        set_tab($(this).attr('related'));
    });
});
