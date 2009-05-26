$(document).ready(function()
{
    /* blog discover */

    var blog_name                 = "";
    var blog_type                 = "";
    var blog_type_label           = "";
    var blog_type_accepted        = false;
    var blog_type_maintenance     = false;
    var blog_version              = "";
    var blog_version_label        = "";
    var blog_revision             = 0;
    var blog_url                  = "";
    var blog_url_accepted         = false;
    var blog_manager_url          = "";
    var blog_manager_url_accepted = false;
    var blog_username             = "";
    var blog_password             = "";
    var blog_login_accepted       = false;
    var blog_publication_accepted = false;

    /* dom objects */

    blog_name_input = $("input[name='blog_name']");

    discover_url_input_row_ = $("tr#discover_url_input_row");
    discover_url_input      = $("input[name='discover_url_input']");
    discover_url_lnk_       = $("a#discover_url_lnk");
    discover_url_result_ro_ = $("tr#discover_url_result_row");
    discover_url_display_   = $("div#discover_url_display");
    discover_url_change_ln_ = $("a#discover_url_change_lnk");

    blog_type_row_    = $("tr#blog_type_row");
    blog_type_displa_ = $("div#blog_type_display");

    manager_url_input_row_ = $("tr#manager_url_input_row");
    manager_url_input      = $("input[name='manager_url_input']");
    manager_url_lnk        = $("a#manager_url_lnk");
    manager_url_result_ro_ = $("tr#manager_url_result_row");
    manager_url_display_   = $("div#manager_url_display");
    manager_url_change_ln_ = $("a#manager_url_change_lnk");
    manager_url_check_lnk_ = $("a#manager_url_check_lnk");

    login_table_         = $("table#login_table");
    login_username_inpu_ = $("input[name='username_input']");
    login_password_inpu_ = $("input[name='password_input']");
    login_check_lnk      = $("a#login_check_lnk");

    buttons_table = $("table#blog_buttons_table");
    button_submit = $("input[name='add_submit_button']");

    blog_add_message_ = $("tr#blog_add_message");

    /* url */

    function commitURL()
    {
        discover_url_input.val(blog_url);
        discover_url_input_row_.hide();
        discover_url_display_.text(blog_url);
        discover_url_result_ro_.show();
    }

    function resetURL()
    {
        blog_url = "";
        blog_url_accepted = false;
        discover_url_display_.text("");
        discover_url_result_ro_.hide();
        discover_url_input_row_.show();
        resetBlogType();
    }

    /* blog type */

    function commitBlogType()
    {
        blog_type_displa_.text(blog_type_label + " / " + blog_version_label);
        blog_type_row_.show();
    }

    function resetBlogType()
    {
        blog_type             = "";
        blog_type_label       = "";
        blog_type_accepted    = false;
        blog_type_maintenance = false;
        blog_version          = "";
        blog_version_label    = "";
        blog_revision         = 0;
        blog_type_displa_.text("");
        blog_type_row_.hide();
        resetManagerURL();
    }

    /* manager url */

    function commitManagerURL()
    {
        manager_url_display_.text(blog_manager_url);
        /* this not works on IE
        if(manager_url_input_row_.is(':visible'))
        {
            manager_url_result_ro_.show();
        }
        */
        manager_url_input.val(blog_manager_url);
        manager_url_input_row_.hide();
    }

    function resetManagerURL()
    {
        blog_manager_url = "";
        blog_manager_url_accepted = false;
        manager_url_display_.text("");
        manager_url_result_ro_.hide();
        manager_url_input_row_.hide();
        resetLogin();
    }

    function changeManagerURL()
    {
        blog_manager_url_accepted = false;
        manager_url_display_.text("");
        manager_url_result_ro_.hide();
        manager_url_input_row_.show();
        resetLogin();
    }

    function commitLogin()
    {
        login_username_inpu_.val(blog_username);
        login_password_inpu_.val(blog_password);
        login_table_.show();
        commitButtons();
    }

    function resetLogin()
    {
        blog_username = "";
        blog_password = "";
        login_username_inpu_.val("");
        login_password_inpu_.val("");
        blog_login_accepted = false;
        blog_publication_accepted = false;
        login_table_.hide();
        resetButtons();
    }

    function commitButtons()
    {
        buttons_table.show();
    }

    function resetButtons()
    {
        buttons_table.hide();
    }

    /* ACTIONS */

    /* on error */

    function err()
    {
        alert('erro!');
    }

    /* check url action */

    function msg(m)
    {
        blog_add_message_.find("td").html(m);
        (m=="") ? (blog_add_message_.hide()) : (blog_add_message_.show());
    }

    function discoverURL()
    {
        if((blog_url = discover_url_input.val()) == "")
        {
            msg("<?php echo $this->translation()->form_incomplete ?>");
            return null;
        }

        var parameters = { url: blog_url }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('blog', 'discover') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function () { set_active_request(true); msg(""); },
            complete: function ()   { set_active_request(false); },
            success: function (xml) 
            { 
                data = $(xml).find('data');
                result = data.find('result');

                blog_type                 =  result.find('type').text();
                blog_type_label           =  result.find('type_label').text();
                blog_type_accepted        = (result.find('type_accepted').text() == "true");
                blog_type_maintenance     = (result.find('maintenance').text() == "true");
                blog_version              =  result.find('version').text();
                blog_version_label        =  result.find('version_label').text();
                blog_revision             =  result.find('revision').text();
                blog_url                  =  result.find('url').text();
                blog_url_accepted         = (result.find('url_accepted').text() == "true");
                blog_manager_url          =  result.find('manager_url').text();
                blog_manager_url_accepted = (result.find('manager_url_accepted').text() == "true");
                blog_username             =  result.find('username').text();
                blog_password             =  result.find('password').text();
                blog_login_accepted       = (result.find('login_accepted').text() == "true");
                blog_publication_accepted = (result.find('publication_accepted').text() == "true");

                failed = false;

                if(failed ==false && blog_type_accepted ==false)
                {
                    failed = true;
                    msg("<?php echo $this->translation()->type_failed ?>");
                    resetURL();
                }

                if(failed ==false && blog_type_maintenance == true)
                {
                    failed = true;
                    msg("<?php echo $this->translation()->type_maintenance ?>");
                }

                if(failed ==false && blog_url_accepted ==false)
                {
                    failed = true;
                    msg("<?php echo $this->translation()->url_not_accepted ?>");
                    resetURL();
                }

                if(failed ==false)
                {
                    commitURL();
                    commitBlogType();
                    commitManagerURL();
                    commitLogin();

                    if(blog_manager_url_accepted ==false)
                    {
                        failed = true;
                        msg("<?php echo $this->translation()->manager_url_not_accepted ?>");
                        changeManagerURL();
                    }
                }
            }, 
            error: function () { err(); }
        });
    }

    /* check manager url */

    function checkManagerURL()
    {
        if((blog_manager_url = manager_url_input.val()) == "")
        {
            msg("<?php echo $this->translation()->form_incomplete ?>");
            return null;
        }

        var parameters = { url:     blog_manager_url, 
                           type:    blog_type, 
                           version: blog_version };

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('blog', 'check') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function () { set_active_request(true); msg(""); },
            complete: function ()   { set_active_request(false); },
            success: function (xml) 
            { 
                data = $(xml).find('data');
                result = data.find('result');

                blog_manager_url          =  result.find('manager_url').text();
                blog_manager_url_accepted = (result.find('manager_url_accepted').text() == "true");

                if(blog_manager_url_accepted == true)
                {
                    commitManagerURL();
                }
                else
                {
                    msg("<?php echo $this->translation()->manager_url_not_accepted ?>");
                    changeManagerURL();
                }
            }, 
            error: function () { err(); }
        });
    }

    function checkManagerLogin()
    {
        alert('-- TODO --');
    }

    function addSubmit()
    {
        blog_name     = blog_name_input.val();
        blog_username = login_username_inpu_.val();
        blog_password = login_password_inpu_.val();

        if(blog_name     == "" || 
           blog_username == "" || 
           blog_password == "")
        {
            msg("<?php echo $this->translation()->form_incomplete ?>");
            return null;
        }

        var parameters = { blog_name          : blog_name, 
                           blog_url           : blog_url,
                           blog_manager_url   : blog_manager_url,
                           blog_username      : blog_username,
                           blog_password      : blog_password,
                           blog_type          : blog_type,
                           blog_version       : blog_version,
                           blog_revision      : blog_revision };

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('blog', 'add') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function () { set_active_request(true); msg(""); },
            complete: function ()   { set_active_request(false); },
            success: function (xml) 
            { 
                data = $(xml).find('data');
                added = (data.find('added').text() == "true");
                message = data.find('message').text();

                if(added == true)
                {
                    window.location = "<?php B_Helper::url('blog') ?>";
                }
                else
                {
                    msg(message);
                }
            }, 
            error: function () { err(); }
        });
    }

    /* TRIGGERS */

    // $("input[name='discover_url']").blur(function()
    // {
    //     check();
    // });

    discover_url_lnk_.click(function()
    {
        if(active_request==false)
        {
            discoverURL();
        }
    });

    discover_url_input.keypress(function(e) 
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            discover_url_lnk_.click();
        }
    });

    discover_url_change_ln_.click(function()
    {
        if(active_request==false)
        {
            resetURL();
        }
    });

    manager_url_check_lnk_.click(function()
    {
        if(active_request==false)
        {
            checkManagerURL();
        }
    });

    login_check_lnk.click(function()
    {
        if(active_request==false)
        {
            checkManagerLogin();
        }
    });

    button_submit.click(function() 
    {
        if(active_request==false)
        {
            addSubmit();
        }
    });

    // add_cancel_button.click(function() 
    // {
    //     window.location = "<?php B_Helper::url('blog') ?>";
    // });
});
