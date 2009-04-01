$(document).ready(function()
{
    /* DEFAULTS */

    var ar = false; /* active request */
    var fc = false; /* form complete */

    /* spinner */

    $.b_spinner
    ({
        image: "<?php B_Helper::img_src('spinner.gif') ?>",
        message: "... <?php echo $this->translation->application_loading ?>"
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
        $("input[name='input_url']").val(url);
        $("input[name='input_url']").hide();
        $("#input_url_ro").text(url);
        $("#input_url_ro").show();
        $("#check_url").hide();
        $("#change_url").show();
    }

    function changeURL()
    {
        $("#input_url_ro").text("");
        $("#input_url_ro").hide();
        $("input[name='input_url']").show();
        $("#change_url").hide();
        $("#check_url").show();
        changeBlogType();
        resetManagerURL();
    }

    /* blog type */

    function commitBlogType(name)
    {
        $("#blog_type_row").show();
        $("#input_blog_type_ro").text(name);
    }

    function changeBlogType()
    {
        $("#input_blog_type_ro").text("");
        $("#blog_type_row").hide();
    }

    /* manager url */

    function commitManagerURL(url)
    {
        $("input[name='manager_url']").val(url);
        $("input[name='manager_url']").hide();
        $("#check_manager_url").hide();
        $("#input_manager_url_ro").show();
        $("#input_manager_url_ro").text(url);
        $("#check_manager_login").show();
        $("#manager_login_row").show();
        fc = true;
    }

    function changeManagerURL()
    {
        $("#manager_url_row").show();
        $("#input_manager_url_ro").text("");
        $("#input_manager_url_ro").hide();
        $("input[name='manager_url']").show();
        $("#check_manager_url").show();
        $("#check_manager_login").hide();
        $("#manager_login_row").hide();
        fc = false;
    }

    function resetManagerURL()
    {
        $("input[name='manager_url']").val("");
        $("#input_manager_url_ro").text("");
        $("#manager_url_row").hide();
        $("#check_manager_login").hide();
        $("#manager_login_row").hide();
        fc = false;
    }

    /* ACTIONS */

    /* on error */

    function onError()
    {
        alert('erro!');
        /* window.location = "<?php B_Helper::url('blog','add') ?>"; */
    }

    /* check url action */

    function blogaddmsg(m)
    {
        _id = "blogaddmessage"; $("#" + _id + " td").html(m); _tr = $("#" + _id);
        (m=="") ? (_tr.hide()) : (_tr.show());
    }

    function checkURL()
    {
        if(ar == true)
        {
            return null;
        }

        var url = $("input[name='input_url']").val();

        if(url == "")
        {
            $.b_alert("informe o endereço do Blog");
            return null;
        }

        var parameters = { url: url }

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
                data                 = $(xml).find('data');
                url                  = data.find('url').text();
                url_accepted         = data.find('url_accepted').text();
                blog_type_name        = data.find('blog_type_name').text();
                blog_type_version     = data.find('blog_type_version').text();
                blog_type_accepted    = data.find('blog_type_accepted').text();
                blog_type_maintenance = data.find('blog_type_maintenance').text();
                manager_url          = data.find('manager_url').text();
                manager_url_accepted = data.find('manager_url_accepted').text();

                /* url */

                if(url_accepted == "true") 
                {
                    commitURL(url);
                }
                else
                {
                    changeURL();
                    blogaddmsg("<?php echo $this->translation->url_not_accepted ?>");
                }

                /* blog type */

                if(blog_type_accepted == "true")
                {
                    commitBlogType(blog_type_name + " (" + blog_type_version + ")");
                }
                else
                {
                    changeURL();
                    blogaddmsg("<?php echo $this->translation->blog_type_not_accepted ?>");
                }

                if(blog_type_maintenance == "true")
                {
                    blogaddmsg("<?php echo $this->translation->blog_type_maintenance ?>");
                }

                /* manager url */

                if(blog_type_accepted == "true")
                {
                    commitManagerURL(manager_url);

                    if(manager_url_accepted == "false")
                    {
                        blogaddmsg("<?php echo $this->translation->url_manager_not_accepted ?>");
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
        if(ar == true)
        {
            return null;
        }

        var manager_url = $("input[name='manager_url']").val();

        if(manager_url == "")
        {
            blogaddmsg("<?php echo $this->translation->manager_url_blank ?>");
            return null;
        }

        var parameters = { manager: manager_url }

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
                data                 = $(xml).find('data');
                manager_url          = data.find('manager_url').text();
                manager_url_accepted = data.find('manager_url_accepted').text();

                commitManagerURL(manager_url);

                if(manager_url_accepted == "false")
                {
                    blogaddmsg("<?php echo $this->translation->url_manager_not_accepted ?>");
                    changeManagerURL();
                }
            }, 
            error: function () { onError(); }
        });
    }

    /* check manager login */

    function checkManagerLogin()
    {
        if(ar == true)
        {
            return null;
        }

        var username = $("input[name='manager_username']").val();
        var password = $("input[name='manager_password']").val();

        if(username == "" || password == "")
        {
            blogaddmsg("informe o usuário e senha do gerenciador");
            return null;
        }

        var parameters = { username: username, password: password }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('blog', 'check') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function () { sp(true);  },
            complete: function ()   { sp(false); },
            success: function (xml) 
            { 
                var login_status = $(xml).find('login_status').text();

                if(login_status = "ok")
                {
                    blogaddmsg("<?php echo $this->translation->login_verified ?>");
                }
                else
                {
                    blogaddmsg("<?php echo $this->translation->login_invalid ?>");
                }
            }, 
            error: function () { onError(); }
        });
    }

    /* submit */

    function addSubmit()
    {
        if(ar == true)
        {
            return null;
        }

        name = $("input[name='name']").val();
        username = $("input[name='manager_username']").val();
        password = $("input[name='manager_password']").val();

        if(name == "" || username == "" || password == "")
        {
            blogaddmsg("<?php echo $this->translation->invalid_form ?>");
            return null;
        }

        if(fc == false)
        {
            blogaddmsg("<?php echo $this->translation->blog_url_not_verified ?>");
            return null;
        }

        parameters = { name: name, 
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
                    window.location = "<?php B_Helper::url('dashboard') ?>";
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
    $("input[name='input_url']").blur(function()
    {
        check();
    });
    */

    $("#check_url").click(function()
    {
        checkURL();
    });

    $("input[name='input_url']").keypress(function(e) 
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            $("#check_url").click();
        }
    });

    $("#change_url").click(function()
    {
        changeURL();
    });

    $("#check_manager_url").click(function()
    {
        checkManagerURL();
    });

    $("#check_manager_login").click(function()
    {
        checkManagerLogin();
    });

    $("input[name='addsubmit']").click(function() 
    {
        addSubmit();
    });

    $("input[name='addcancel']").click(function() 
    {
        window.location = "<?php B_Helper::url('dashboard') ?>";
    });
});
