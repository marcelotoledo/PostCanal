$(document).ready(function()
{
    var active_request = false;

    var url = "";

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

    /* url switches */

    function commitURL(url)
    {
        $("input[@name='input_url']").val(url);
        $("input[@name='input_url']").hide();
        $("#input_url_ro").text(url);
        $("#input_url_ro").show();
        $("#check_url").hide();
        $("#change_url").show();
    }

    function changeURL()
    {
        $("#input_url_ro").text("");
        $("#input_url_ro").hide();
        $("input[@name='input_url']").show();
        $("#change_url").hide();
        $("#check_url").show();
        changeCMSType();
        resetManagerURL();
    }

    /* cms type switches */

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

    /* manager url switches */

    function commitManagerURL(url)
    {
        $("input[@name='manager_url']").val(url);
        $("input[@name='manager_url']").hide();
        $("#input_manager_url_ro").show();
        $("#input_manager_url_ro").text(url);
    }

    function changeManagerURL()
    {
        $("#manager_url_row").show();
        $("#input_manager_url_ro").text("");
        $("#input_manager_url_ro").hide();
        $("input[@name='manager_url']").show();
    }

    function resetManagerURL()
    {
        $("input[@name='manager_url']").val("");
        $("#input_manager_url_ro").text("");
        $("#manager_url_row").hide();
    }

    /* check url */

    function checkURL()
    {
        if(active_request == true)
        {
            return null;
        }

        url = $("input[@name='input_url']").val();

        if(url == "")
        {
            $.ab_alert("informe o endereço do CMS");
            return null;
        }

        parameters = { url: url }

        $.ajax
        ({
            type: "POST",
            url: "<?php $this->url('cms', 'check') ?>",
            dataType: "json",
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
                /* url status */

                if(data.url_status == "status_ok") 
                {
                    commitURL(data.url);
                }
                else if(data.url_status == "status_failed")
                {
                    changeURL();
                    $.ab_alert("Verifique se o endereço informado é valido");
                }
                else if(data.url_status == "status_4xx")
                {
                    changeURL();
                    $.ab_alert("Endereço não encontrado");
                }
                else if(data.url_status == "status_3xx" || 
                        data.url_status == "status_5xx")
                {
                    changeURL();
                    $.ab_alert("O endereço informado possui erros ou está" +
                               "sendo redirecionado para outro local");
                }

                /* cms type */

                if(data.cms_type_status == "ok")
                {
                    commitCMSType(data.cms_type_name + " (" + 
                                  data.cms_type_version + ")");
                }
                else if(data.cms_type_status == "unknown")
                {
                    changeURL();
                    $.ab_alert("Não foi possível determinar o tipo de CMS " + 
                               "para o endereço informado");
                }
                else if(data.cms_type_status == "maintenance")
                {
                    $.ab_alert("O tipo de CMS " + 
                               data.cms_type_name + " (" + 
                               data.cms_type_version + ") " +
                               " está em manutenção. Tente novamente mais tarde");
                }

                /* manager url */

                if(data.cms_type_status == "ok")
                {
                    commitManagerURL(data.manager_url);

                    if(data.manager_status != "ok")
                    {
                        changeManagerURL();
                    }
                }
            }, 
            error: function () 
            { 
                /* window.location = "<?php $this->url('dashboard') ?>"; */
            }
        });
    }

    /* triggers */

    /* 
    $("input[@name='input_url']").blur(function()
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
        manager_username = $("input[@name='manager_username']").val();
        manager_password = $("input[@name='manager_password']").val();

        if(name == "" || manager_username == "" || manager_password == "")
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
            dataType: "json",
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
