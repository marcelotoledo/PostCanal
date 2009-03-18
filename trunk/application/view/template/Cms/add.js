$(document).ready(function()
{
    /* DEFAULTS */

    var ar = false; /* active request */
    var fc = false; /* form complete */


    /* SWITCHES */

    /* spinner */

    function sp(b)
    {
        if((ar = b) == true)
        {
            $.b_spinner_start
            ({
                height: 32, width: 32,
                image: "<?php B_Helper::img_src('spinner.gif') ?>",
                message: "... <?php echo $this->translation->application_loading ?>"
            });
        }
        else
        {
            $.b_spinner_stop();
        }
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
        changeCMSType();
        resetManagerURL();
    }

    /* cms type */

    function commitCMSType(name)
    {
        $("#cms_type_row").show();
        $("#input_cms_type_ro").text(name);
    }

    function changeCMSType()
    {
        $("#input_cms_type_ro").text("");
        $("#cms_type_row").hide();
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
        /* window.location = "<?php B_Helper::url('cms','add') ?>"; */
    }

    /* check url action */

    function checkURL()
    {
        if(ar == true)
        {
            return null;
        }

        var url = $("input[name='input_url']").val();

        if(url == "")
        {
            $.b_alert("informe o endereço do CMS");
            return null;
        }

        var parameters = { url: url }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('cms', 'check') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function () { sp(true);  },
            complete: function ()   { sp(false); },
            success: function (xml) 
            { 
                data                 = $(xml).find('data');
                url                  = data.find('url').text();
                url_accepted         = data.find('url_accepted').text();
                cms_type_name        = data.find('cms_type_name').text();
                cms_type_version     = data.find('cms_type_version').text();
                cms_type_accepted    = data.find('cms_type_accepted').text();
                cms_type_maintenance = data.find('cms_type_maintenance').text();
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
                    $.b_alert("<?php echo $this->translation->url_not_accepted ?>");
                }

                /* cms type */

                if(cms_type_accepted == "true")
                {
                    commitCMSType(cms_type_name + " (" + cms_type_version + ")");
                }
                else
                {
                    changeURL();
                    $.b_alert("<?php echo $this->translation->cms_type_not_accepted ?>");
                }

                if(cms_type_maintenance == "true")
                {
                    $.b_alert("<?php echo $this->translation->cms_type_maintenance ?>");
                }

                /* manager url */

                if(cms_type_accepted == "true")
                {
                    commitManagerURL(manager_url);

                    if(manager_url_accepted == "false")
                    {
                        $.b_alert("<?php echo $this->translation->url_manager_not_accepted ?>");
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
            $.b_alert("informe o endereço do gerenciador");
            return null;
        }

        var parameters = { manager: manager_url }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('cms', 'check') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function () { sp(true);  },
            complete: function ()   { sp(false); },
            success: function (xml) 
            { 
                data                 = $(xml).find('data');
                manager_url          = data.find('manager_url').text();
                manager_url_accepted = data.find('manager_url_accepted').text();

                commitManagerURL(manager_url);

                if(manager_url_accepted == "false")
                {
                    $.b_alert("<?php echo $this->translation->url_manager_not_accepted ?>");
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
            $.b_alert("informe o usuário e senha do gerenciador");
            return null;
        }

        var parameters = { username: username, password: password }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('cms', 'check') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function () { sp(true);  },
            complete: function ()   { sp(false); },
            success: function (xml) 
            { 
                var login_status = $(xml).find('login_status').text();

                if(login_status = "ok")
                {
                    $.b_alert("Usuário e senha verificados com sucesso");
                }
                else
                {
                    $.b_alert("O usuário ou senha informados não são válidos");
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
            $.b_alert("Preencha o formulário corretamente");
            return null;
        }

        if(fc == false)
        {
            $.b_alert("O endereço do CMS precisa ser verificado");
            return null;
        }

        parameters = { name: name, 
                       username: username, 
                       password: password }

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('cms', 'add') ?>",
            dataType: "xml",
            data: parameters,
            beforeSend: function () { sp(true);  },
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
                    if(message != "") $.b_alert(message);
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
