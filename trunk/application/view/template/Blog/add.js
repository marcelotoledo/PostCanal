var mytpl = null
var bdisc = null;


function commit_url()
{
    mytpl.discover_url_input.val(bdisc.url);
    mytpl.discover_url_input_row.hide();
    mytpl.discover_url_display.text(bdisc.url);
    mytpl.discover_url_result_row.show();
}

function reset_url()
{
    bdisc.url = "";
    bdisc.url_accepted = false;
    mytpl.discover_url_display.text("");
    mytpl.discover_url_result_row.hide();
    mytpl.discover_url_input_row.show();
    reset_blog_type();
}

function commit_blog_type()
{
    mytpl.blog_type_display.text(bdisc.type_label + " / " + bdisc.version_label);
    mytpl.blog_type_row.show();
}

function reset_blog_type()
{
    bdisc.type             = "";
    bdisc.type_label       = "";
    bdisc.type_accepted    = false;
    bdisc.type_maintenance = false;
    bdisc.version          = "";
    bdisc.version_label    = "";
    bdisc.revision         = 0;
    mytpl.blog_type_display.text("");
    mytpl.blog_type_row.hide();
    reset_manager_url();
}

function commit_manager_url()
{
    mytpl.manager_url_display.text(bdisc.manager_url);
    mytpl.manager_url_input.val(bdisc.manager_url);
    mytpl.manager_url_input_row.hide();
}

function reset_manager_url()
{
    bdisc.manager_url = "";
    bdisc.manager_url_accepted = false;
    mytpl.manager_url_display.text("");
    mytpl.manager_url_result_row.hide();
    mytpl.manager_url_input_row.hide();
    reset_login();
}

function change_manager_url()
{
    bdisc.manager_url_accepted = false;
    mytpl.manager_url_display.text("");
    mytpl.manager_url_result_row.hide();
    mytpl.manager_url_input_row.show();
    reset_login();
}

function commit_login()
{
    mytpl.login_username_input.val(bdisc.username);
    mytpl.login_password_input.val(bdisc.password);
    mytpl.login_table.show();
    commit_buttons();
}

function reset_login()
{
    bdisc.username = "";
    bdisc.password = "";
    mytpl.login_username_input.val("");
    mytpl.login_password_input.val("");
    bdisc.login_accepted = false;
    bdisc.publication_accepted = false;
    mytpl.login_table.hide();
    reset_buttons();
}

function commit_buttons()
{
    mytpl.buttons_table.show();
}

function reset_buttons()
{
    mytpl.buttons_table.hide();
}

function form_message(m)
{
    (m=="") ?
        mytpl.blog_add_message.hide().find("td").html("") :
        mytpl.blog_add_message.show().find("td").html(m) ;
}

function discover_url()
{
    if((bdisc.url = mytpl.discover_url_input.val()) == "")
    {
        form_message("<?php echo $this->translation()->form_incomplete ?>");
        return null;
    }

    $.ajax
    ({
        type: "POST",
        url: "<?php B_Helper::url('blog', 'discover') ?>",
        dataType: "xml",
        data: { url: bdisc.url },
        beforeSend: function () { set_active_request(true); form_message(""); },
        complete: function ()   { set_active_request(false); },
        success: function (xml) 
        { 
            var _d = $(xml).find('data');
            var _r = _d.find('result');

            bdisc.type                 =  _r.find('type').text();
            bdisc.type_label           =  _r.find('type_label').text();
            bdisc.type_accepted        = (_r.find('type_accepted').text() == "true");
            bdisc.type_maintenance     = (_r.find('maintenance').text() == "true");
            bdisc.version              =  _r.find('version').text();
            bdisc.version_label        =  _r.find('version_label').text();
            bdisc.revision             =  _r.find('revision').text();
            bdisc.url                  =  _r.find('url').text();
            bdisc.url_accepted         = (_r.find('url_accepted').text() == "true");
            bdisc.manager_url          =  _r.find('manager_url').text();
            bdisc.manager_url_accepted = (_r.find('manager_url_accepted').text() == "true");
            bdisc.username             =  _r.find('username').text();
            bdisc.password             =  _r.find('password').text();
            bdisc.login_accepted       = (_r.find('login_accepted').text() == "true");
            bdisc.publication_accepted = (_r.find('publication_accepted').text() == "true");

            var _failed = false;

            if(_failed ==false && bdisc.type_accepted ==false)
            {
                _failed = true;
                form_message("<?php echo $this->translation()->type_failed ?>");
                mytpl.blog_types_info.show();
                reset_url();
            }

            if(_failed ==false && bdisc.type_maintenance == true)
            {
                _failed = true;
                form_message("<?php echo $this->translation()->type_maintenance ?>");
            }

            if(_failed ==false && bdisc.url_accepted ==false)
            {
                _failed = true;
                form_message("<?php echo $this->translation()->url_not_accepted ?>");
                reset_url();
            }

            if(_failed ==false)
            {
                commit_url();
                commit_blog_type();
                commit_manager_url();
                commit_login();

                if(bdisc.manager_url_accepted ==false)
                {
                    _failed = true;
                    form_message("<?php echo $this->translation()->manager_url_not_accepted ?>");
                    change_manager_url();
                }
            }
        }, 
        error: function () { server_error(); }
    });
}

/* check manager url */

function check_manager_url()
{
    if((bdisc.manager_url = manager_url_input.val()) == "")
    {
        form_message("<?php echo $this->translation()->form_incomplete ?>");
        return null;
    }

    $.ajax
    ({
        type: "POST",
        url: "<?php B_Helper::url('blog', 'check') ?>",
        dataType: "xml",
        data: { url:     bdisc.manager_url, 
                type:    bdisc.type, 
                version: bdisc.version },
        beforeSend: function () { set_active_request(true); form_message(""); },
        complete: function ()   { set_active_request(false); },
        success: function (xml) 
        { 
            var _d = $(xml).find('data');
            var _r = data.find('result');

            bdisc.manager_url          =  _r.find('manager_url').text();
            bdisc.manager_url_accepted = (_r.find('manager_url_accepted').text() == "true");

            if(bdisc.manager_url_accepted == true)
            {
                commit_manager_url();
            }
            else
            {
                form_message("<?php echo $this->translation()->manager_url_not_accepted ?>");
                change_manager_url();
            }
        }, 
        error: function () { server_error(); }
    });
}

function check_manager_login()
{
    // TODO
    return null;
}

function add_submit()
{
    bdisc.name     = mytpl.blog_name.val();
    bdisc.username = mytpl.login_username_input.val();
    bdisc.password = mytpl.login_password_input.val();

    if(bdisc.name     == "" || 
       bdisc.username == "" || 
       bdisc.password == "")
    {
        form_message("<?php echo $this->translation()->form_incomplete ?>");
        return null;
    }

    $.ajax
    ({
        type: "POST",
        url: "<?php B_Helper::url('blog', 'add') ?>",
        dataType: "xml",
        data: { blog_name        : bdisc.name, 
                blog_url         : bdisc.url,
                blog_manager_url : bdisc.manager_url,
                blog_username    : bdisc.username,
                blog_password    : bdisc.password,
                blog_type        : bdisc.type,
                blog_version     : bdisc.version,
                blog_revision    : bdisc.revision },
        beforeSend: function () { set_active_request(true); form_message(""); },
        complete: function ()   { set_active_request(false); },
        success: function (xml) 
        { 
            var _d = $(xml).find('data');

            if(_d.find('added').text()=="true")
            {
                window.location = "<?php B_Helper::url('blog') ?>";
            }
            else
            {
                form_message(_d.find('message').text());
            }
        }, 
        error: function () { server_error(); }
    });
}


$(document).ready(function()
{
    mytpl =
    {
        blog_name               : $("#blog_name"),
        discover_url_input_row  : $("#discover_url_input_row"),
        discover_url_input      : $("#discover_url_input"),
        discover_url_lnk        : $("#discover_url_lnk"),
        discover_url_result_row : $("#discover_url_result_row"),
        discover_url_display    : $("#discover_url_display"),
        discover_url_change_lnk : $("#discover_url_change_lnk"),
        blog_type_row           : $("#blog_type_row"),
        blog_type_display       : $("#blog_type_display"),
        manager_url_input_row   : $("#manager_url_input_row"),
        manager_url_input       : $("#manager_url_input"),
        manager_url_check_lnk   : $("#manager_url_check_lnk"),
        manager_url_result_row  : $("#manager_url_result_row"),
        manager_url_display     : $("#manager_url_display"),
        manager_url_change_lnk  : $("#manager_url_change_lnk"),
        manager_url_check_lnk   : $("#manager_url_check_lnk"),
        login_table             : $("#login_table"),
        login_username_input    : $("#username_input"),
        login_password_input    : $("#password_input"),
        // login_check_lnk         : $("#login_check_lnk"), // TODO
        buttons_table           : $("#blog_buttons_table"),
        button_submit           : $("#add_submit_button"),
        blog_add_message        : $("#blog_add_message"),
        blog_types_info         : $("#blog_types_info")
    };

    /* blog discover */

    bdisc =
    {
        name                 : "",
        type                 : "",
        type_label           : "",
        type_accepted        : false,
        type_maintenance     : false,
        version              : "",
        version_label        : "",
        revision             : 0,
        url                  : "",
        url_accepted         : false,
        manager_url          : "",
        manager_url_accepted : false,
        username             : "",
        password             : "",
        login_accepted       : false,
        publication_accepted : false
    };

    /* triggers */

    mytpl.discover_url_lnk.click(function()
    {
        if(active_request==false)
        {
            mytpl.blog_types_info.hide();
            discover_url();
        }
    });

    mytpl.discover_url_input.keypress(function(e) 
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            mytpl.discover_url_lnk.click();
        }
    });

    mytpl.discover_url_change_lnk.click(function()
    {
        if(active_request==false)
        {
            reset_url();
        }
    });

    mytpl.manager_url_check_lnk.click(function()
    {
        if(active_request==false)
        {
            check_manager_url();
        }
    });

    /* TODO
    mytpl.login_check_lnk.click(function()
    {
        if(active_request==false)
        {
            check_manager_login();
        }
    });
    */

    mytpl.button_submit.click(function() 
    {
        if(active_request==false)
        {
            add_submit();
        }
    });

    mytpl.blog_name.focus();
});
