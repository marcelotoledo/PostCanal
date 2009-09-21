var my_template = null;

function set_tab(i)
{
    my_template.tabhead.find("div.tabitem").removeClass('tabitem-selected');
    my_template.tabgroup.find("div.tabcontainer").hide();
    my_template.tabhead.find("div.tabitem[related='" + i + "']").addClass('tabitem-selected');
    my_template.tabgroup.find("#" + i).show();
}

function edit_message(m)
{
    my_template.editmessage.text(m);
}

function edit_submit_callback(d)
{
    if(d.length==0) { server_error(); return false; }
    if(d.find('saved').text()=="true") { flash_message("<?php echo $this->translation()->saved ?>"); } else { /* void */ }
}

function edit_submit()
{
    var _data = { name            : my_template.name.val(),
                  local_territory : my_template.territory.val(),
                  local_timezone  : my_template.timezone.val() // ,
               /* local_culture   : my_template.culture.val() */ };

    do_request('POST', './profile/edit', _data, edit_submit_callback);
}

function pwdchange_message(m)
{
    my_template.pwdchangemessage.text(m);
}

function pwdchange_after()
{
    my_template.currentpwd.val('');
    my_template.newpwd.val('');
    my_template.confirmpwd.val('');
}

function pwdchange_submit_callback(d)
{
    if(d.length==0) { server_error(); return false; }
    if(d.find('updated').text()=="true") { flash_message("<?php echo $this->translation()->saved ?>"); pwdchange_after(); } else { pwdchange_message(d.find('message').text()); }

}

function pwdchange_submit()
{
    var _data = { current   : my_template.currentpwd.val(),
                  password  : my_template.newpwd.val(), 
                  passwordc : my_template.confirmpwd.val() };

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
    my_template.emlchangemessage.text(m);
}

function emlchange_submit_callback(d)
{
    if(d.length==0) { server_error(); return false; }
    if(d.find('accepted').text()=="true") { flash_message("<?php echo $this->translation()->saved ?>"); my_template.emlvermsg.text("<?php echo $this->translation()->check_your_inbox_to_validate ?>"); } else { emlchange_message(d.find('message').text()); }
}

function emlchange_submit()
{
    var _data = { new_email : my_template.neweml.val() };

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

    my_template.timezone.html(_l.join("\n"));
    my_template.timezone.attr('disabled', false);
}

function on_blog_change()
{
    // document.location='./reader'; TODO
}

$(document).ready(function()
{
    my_template =
    {
        tabhead          : $("#edittab"),
        tabgroup         : $("#edittabgroup"),
        neweml           : $("#neweml"),
        emlchangelnk     : $("#emlchangelnk"),
        emlchangesubmit  : $("#emlchangesubmit"),
        emlchangecancel  : $("#emlchangecancel"),
        emlchangemessage : $("#emlchangemessage"),
        emlvermsg        : $("#emlvermsg"),
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
        return my_template.territory.find("option:selected").val();
    }

    function load_timezone()
    {
        do_request('GET', './profile/timezone', { territory: selected_territory() }, timezone_populate);
    }
    
    function initialize()
    {
        load_timezone();
    }

    /* triggers */

    my_template.editsubmit.click(function()
    {
        if(active_request==false) { edit_message(''); edit_submit(); }
        $(this).blur();
        return false;
    });

    my_template.pwdchangesubmit.click(function()
    {
        if(active_request==false) { pwdchange_message(''); pwdchange_submit(); }
        $(this).blur();
        return false;
    });

    my_template.neweml.keypress(function(e) 
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            my_template.emlchangesubmit.click();
        }
    });

    my_template.emlchangesubmit.click(function()
    {
        if(active_request==false) { emlchange_message(''); emlchange_submit(); }
    });

    my_template.territory.change(function()
    {
        load_timezone();
        $(this).blur();
    });

    my_template.timezone.change(function()
    {
        $(this).blur();
    });

    my_template.tabhead.find("div.tabitem").click(function()
    {
        set_tab($(this).attr('related'));
    });

    $(document).bind('blog_changed' , function(e)
    {
        on_blog_change();
    });

    initialize();
});
