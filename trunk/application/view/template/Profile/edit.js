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

function edit_submit_callback(d)
{
    if(d.length==0) { server_error(); return false; }
    if(d.find('saved').text()=="true") { flash_message("<?php echo $this->translation()->saved ?>"); } else { /* void */ }
}

function edit_submit()
{
    var _data = { name            : mytpl.name.val(),
                  local_territory : mytpl.territory.val(),
                  local_timezone  : mytpl.timezone.val() // ,
               /* local_culture   : mytpl.culture.val() */ };

    do_request('POST', './profile/edit', _data, edit_submit_callback);
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

function pwdchange_submit_callback(d)
{
    if(d.length==0) { server_error(); return false; }
    if(d.find('updated').text()=="true") { flash_message("<?php echo $this->translation()->saved ?>"); pwdchange_after(); } else { pwdchange_message(d.find('message').text()); }

}

function pwdchange_submit()
{
    var _data = { current   : mytpl.currentpwd.val(),
                  password  : mytpl.newpwd.val(), 
                  passwordc : mytpl.confirmpwd.val() };

    if(_data.current   == "" || _data.password  == "" || _data.passwordc == "")
    {
        pwdchange_message("<?php echo $this->translation()->form_incomplete ?>");
        return false;
    }

    if(_data.password != _data.passwordc)
    {
        pwdchange_message("<?php echo $this->translation()->password_not_match ?>");
        return false;
    }

    do_request('POST', './profile/password', _data, pwdchange_submit_callback);
}

function emlchange_message(m)
{
    mytpl.emlchangemessage.text(m);
}

function emlchange_submit_callback(d)
{
    if(d.length==0) { server_error(); return false; }
    if(d.find('accepted').text()=="true") { flash_message("<?php echo $this->translation()->saved ?><br><small><?php echo $this->translation()->check_your_inbox_to_validate ?></small>"); } else { emlchange_message(d.find('message').text()); }
}

function emlchange_submit()
{
    var _data = { new_email : mytpl.neweml.val() };

    if(_data.new_email=="")
    {
        emlchange_message("<?php echo $this->translation()->form_incomplete ?>");
        return false;
    }

    do_request('POST', './profile/email', _data, emlchange_submit_callback);
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
        do_request('GET', './profile/timezone', { territory: selected_territory() }, timezone_populate);
    }
    
    load_timezone();

    /* triggers */

    mytpl.editsubmit.click(function()
    {
        if(active_request==false) { edit_message(''); edit_submit(); }
    });

    mytpl.pwdchangesubmit.click(function()
    {
        if(active_request==false) { pwdchange_message(''); pwdchange_submit(); }
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
        if(active_request==false) { emlchange_message(''); emlchange_submit(); }
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
