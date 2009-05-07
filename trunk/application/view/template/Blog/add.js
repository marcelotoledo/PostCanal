$(document).ready(function()
{
    /* DEFAULTS */

    var ar = false; /* active request */

    /* blog discovery / add */

    var blog_url = ""
    var blog_manager_url = ""
    var blog_type = ""
    var blog_version = ""
    var blog_revision = 0

    /* spinner */

    $.b_spinner
    ({
        image: "<?php B_Helper::img_src('spinner.gif') ?>",
        message: "... <?php echo $this->translation()->application_loading ?>"
    });

    /* SWITCHES */

    /* spinner */

    function sp(b)
    {
        ((ar = b) == true) ? $.b_spinner_start() : $.b_spinner_stop();
    }

    /* url */

    function commitURL(url)
    {
        blog_url = url;
        $("input[name='discover_url']").val(url);
        $("input[name='discover_url']").hide();
        $("#discover_url_display").text(url);
        $("#discover_url_display").show();
        $("#discover_url").hide();
        $("#change_url").show();
    }

    function changeURL()
    {
        blog_url = "";
        $("#discover_url_display").text("");
        $("#discover_url_display").hide();
        $("input[name='discover_url']").show();
        $("#change_url").hide();
        $("#discover_url").show();
        changeBlogType();
        resetManagerURL();
    }

    /* blog type */

    function commitBlogType(label, type_name, version_name, revision)
    {
        blog_type = type_name;
        blog_version = version_name;
        blog_revision = revision;
        $("#blog_type_row").show();
        $("#blog_type_display").text(label);
    }

    function changeBlogType()
    {
        blog_type = "";
        blog_version = "";
        revision = 0;
        $("#blog_type_display").text("");
        $("#blog_type_row").hide();
    }

    /* manager url */

    function commitManagerURL(url)
    {
        blog_manager_url = url;
        $("input[name='manager_url']").val(url);
        $("input[name='manager_url']").hide();
        $("#check_manager_url").hide();
        $("#manager_url_display").show();
        $("#manager_url_display").text(url);
        $("#check_manager_login").show();
        $("#manager_login_row").show();
    }

    function changeManagerURL()
    {
        blog_manager_url = "";
        $("#manager_url_row").show();
        $("#manager_url_display").text("");
        $("#manager_url_display").hide();
        $("input[name='manager_url']").show();
        $("#check_manager_url").show();
        $("#check_manager_login").hide();
        $("#manager_login_row").hide();
    }

    function resetManagerURL()
    {
        blog_manager_url = "";
        $("input[name='manager_url']").val("");
        $("#manager_url_display").text("");
        $("#manager_url_row").hide();
        $("#check_manager_login").hide();
        $("#manager_login_row").hide();
    }

    /* ACTIONS */

    /* on error */

    function onError()
    {
        alert('erro!');
    }

    /* check url action */

    function blogaddmsg(m)
    {
        _id = "blogaddmessage"; $("#" + _id + " td").html(m); _tr = $("#" + _id);
        (m=="") ? (_tr.hide()) : (_tr.show());
    }

    function discoverURL()
    {
        var url = $("input[name='discover_url']").val();

        if(url == "")
        {
            $.b_alert("<?php echo $this->translation()->form_incomplete ?>");
            return null;
        }

        var parameters = { url: url }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('blog', 'discover') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function () { sp(true); blogaddmsg(""); },
            complete: function ()   { sp(false); },
            success: function (xml) 
            { 
                data = $(xml).find('data');
                result = data.find('result');

                type_name = result.find('type').text();
                type_label = result.find('type_label').text();
                type_accepted = (result.find('type_accepted').text() == "true");
                maintenance = (result.find('maintenance').text() == "true");
                version_name = result.find('version').text();
                version_label = result.find('version_label').text();
                revision = result.find('revision').text();
                url = result.find('url').text();
                url_accepted = (result.find('url_accepted').text() == "true");
                manager_url = result.find('manager_url').text();
                manager_url_accepted = (result.find('manager_url_accepted').text() == "true");
                username = result.find('username').text();

                failed = false;

                if(failed == false && type_accepted == false)
                {
                    failed = true;
                    blogaddmsg("<?php echo $this->translation()->type_failed ?>");
                    changeURL();
                }

                if(failed == false && maintenance == true)
                {
                    failed = true;
                    blogaddmsg("<?php echo $this->translation()->type_maintenance ?>");
                }

                if(failed == false && url_accepted == false)
                {
                    failed = true;
                    blogaddmsg("<?php echo $this->translation()->url_not_accepted ?>");
                    changeURL();
                }

                if(failed == false)
                {
                    commitURL(url);
                    label = type_label + " / " + version_label;
                    commitBlogType(label, type_name, version_name, revision);
                    commitManagerURL(manager_url);
                    $("input[name='manager_username']").val(username);

                    if(manager_url_accepted == false)
                    {
                        failed = true;
                        blogaddmsg("<?php echo $this->translation()->manager_url_not_accepted ?>");
                        changeManagerURL();
                    }
                }
            }, 
            error: function () { onError(); }
        });
    }

    /* check manager url */

    function checkManagerURL()
    {
        var manager_url = $("input[name='manager_url']").val();

        if(manager_url == "")
        {
            blogaddmsg("<?php echo $this->translation()->form_incomplete ?>");
            return null;
        }

        var parameters = { url: manager_url, type: blog_type , version: blog_version}

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('blog', 'check') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function () { sp(true); blogaddmsg(""); },
            complete: function ()   { sp(false); },
            success: function (xml) 
            { 
                data = $(xml).find('data');
                result = data.find('result');

                manager_url = result.find('manager_url').text();
                manager_url_accepted = (result.find('manager_url_accepted').text() == "true");
                if(manager_url_accepted == true)
                {
                    commitManagerURL(manager_url);
                }
                else
                {
                    blogaddmsg("<?php echo $this->translation()->manager_url_not_accepted ?>");
                    changeManagerURL();
                }
            }, 
            error: function () { onError(); }
        });
    }

    function checkManagerLogin()
    {
        alert('-- TODO --');
    }

    function addSubmit()
    {
        blog_name = $("input[name='blog_name']").val();
        username = $("input[name='manager_username']").val();
        password = $("input[name='manager_password']").val();

        if(blog_url == "")
        {
            changeURL();
            blogaddmsg("<?php echo $this->translation()->blog_url_not_verified ?>");
            return null;
        }

        if(blog_manager_url == "")
        {
            changeURL();
            blogaddmsg("<?php echo $this->translation()->blog_manager_url_not_verified ?>");
            return null;
        }

        if(blog_name == "" || username == "" || password == "")
        {
            blogaddmsg("<?php echo $this->translation()->form_incomplete ?>");
            return null;
        }

        if(blog_type == "" || blog_version == "")
        {
            changeURL();
            blogaddmsg("<?php echo $this->translation()->form_error ?>");
            return null;
        }

        var parameters = { name: blog_name, 
                           url: blog_url,
                           manager_url: blog_manager_url,
                           blog_type: blog_type,
                           blog_version: blog_version,
                           blog_revision: blog_revision,
                           username: username, 
                           password: password }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('blog', 'add') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function () { sp(true); blogaddmsg(""); },
            complete: function ()   { sp(false); },
            success: function (xml) 
            { 
                var data = $(xml).find('data');
                var added = data.find('added').text();
                var message = data.find('message').text();

                if(added == "true")
                {
                    window.location = "<?php B_Helper::url('blog') ?>";
                }
                else
                {
                    blogaddmsg(message);
                }
            }, 
            error: function () { onError(); }
        });
    }

    /* TRIGGERS */

    /* 
    $("input[name='discover_url']").blur(function()
    {
        check();
    });
    */

    $("#discover_url").click(function()
    {
        if(ar == false)
        {
            discoverURL();
        }
    });

    $("input[name='discover_url']").keypress(function(e) 
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            $("#discover_url").click();
        }
    });

    $("#change_url").click(function()
    {
        if(ar == false)
        {
            changeURL();
        }
    });

    $("#check_manager_url").click(function()
    {
        if(ar == false)
        {
            checkManagerURL();
        }
    });

    $("#check_manager_login").click(function()
    {
        if(ar == false)
        {
            checkManagerLogin();
        }
    });

    $("input[name='addsubmit']").click(function() 
    {
        if(ar == false)
        {
            addSubmit();
        }
    });

    $("input[name='addcancel']").click(function() 
    {
        window.location = "<?php B_Helper::url('dashboard') ?>";
    });
});
