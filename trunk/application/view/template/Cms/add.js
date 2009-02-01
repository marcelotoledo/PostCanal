$(document).ready(function()
{
    var active_request = false;

    var url_base = "";

    /* spinner */

    function showSpinner()
    {
        $.ab_spinner
        ({
            height: 32, width: 32,
            image: "<?php $this->img_src('spinner/linux_spinner.png') ?>",
            message: "... carregando"
        });
    }

    function hideSpinner()
    {
        $.ab_spinner_stop();
    }

    /* cms url base check */

    function commitUrlBase()
    {
        $("input[@name='input_url_base']").hide();
        $("#commited_url_base").html(url_base);
        $("#commited_url_base").show();
        $("#check_url_base").hide();
        $("#change_url_base").show();
    }

    function changeUrlBase()
    {
        $("#commited_url_base").html("");
        $("#commited_url_base").hide();
        $("input[@name='input_url_base']").val(url_base);
        $("input[@name='input_url_base']").show();
        $("#change_url_base").hide();
        $("#check_url_base").show();
    }

    function checkUrlBase()
    {
        if(active_request == true)
        {
            return null;
        }

        url_base = $("input[@name='input_url_base']").val();

        if(url_base == "")
        {
            $.ab_alert("informe o endereço do CMS");
            return null;
        }

        parameters = { url_base: url_base }

        $.ajax
        ({
            type: "POST",
            url: "<?php $this->url('cms', 'checkUrlBase') ?>",
            dataType: "text",
            data: parameters,
            beforeSend: function ()
            {
                active_request = true;
                showSpinner();
            },
            complete: function ()
            {
                active_request = false;
                hideSpinner();
            },
            success: function (data) 
            { 
                if(data == "url_base_ok") 
                {
                    commitUrlBase();
                }
                else if(data == "url_base_failed")
                {
                    $.ab_alert("Não foi possível verificar o endereço do CMS");
                }
            }, 
            error: function () 
            { 
                window.location = "<?php $this->url('dashboard') ?>";
            }
        });
    }

    $("input[@name='input_url_base']").blur(function()
    {
        checkUrlBase();
    });

    $("#check_url_base").click(function()
    {
        checkUrlBase();
    });

    $("#change_url_base").click(function()
    {
        changeUrlBase();
    });

    /* cancel */

    $("input[@name='addcancel']").click(function() 
    {
        window.location = "<?php $this->url('dashboard') ?>";
    });

    /* submit */

    $("input[@name='addsubmit']").click(function() 
    {
        if(active_request == true)
        {
            return null;
        }

        name = $("input[@name='name']").val();
        admin_username = $("input[@name='admin_username']").val();
        admin_password = $("input[@name='admin_password']").val();

        if(name == "" || admin_username == "" || admin_password == "")
        {
            $.ab_alert("Preencha o formulário corretamente");
            return null;
        }

        parameters = { name: name, 
                       pwdchange: pwdchange, 
                       current_password: current_password, 
                       new_password: new_password, 
                       new_password_confirm: new_password_confirm }

        $.ajax
        ({
            type: "POST",
            url: "<?php $this->url('cms', 'addSave') ?>",
            dataType: "text",
            data: parameters,
            beforeSend: function ()
            {
                active_request = true;
                showSpinner();
            },
            complete: function ()
            {
                active_request = false;
                hideSpinner();
            },
            success: function (data) 
            { 
                if(data == "add_save_ok") 
                {
                    window.location = "<?php $this->url('dashboard') ?>";
                }
                else if(data == "add_save_failed")
                {
                    $.ab_alert("Não foi possível adicionar um novo CMS");
                }
            }, 
            error: function () 
            { 
                window.location = "<?php $this->url('dashboard') ?>";
            }
        });
    });
});
