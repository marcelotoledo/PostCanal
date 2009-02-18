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

    function commitURL()
    {
        $("input[@name='input_url']").attr("disabled", true);
        $("#check_url").hide();
        $("#change_url").show();
    }

    function changeURL()
    {
        $("input[@name='input_url']").attr("disabled", false);
        $("#change_url").hide();
        $("#check_url").show();
        changeCMSType();
    }

    /* cms type switches */

    function commitCMSType(name)
    {
        $("select[@name='cms_type']").attr("disabled", false);
        $("select[@name='cms_type']").html("<option>" + name + "</option>");
    }

    function changeCMSType()
    {
        $("select[@name='cms_type']").attr("disabled", true);
        $("select[@name='cms_type']").html("");
    }

    /* manager url switches */

    function commitManagerURL(url)
    {
        $("input[@name='manager_url']").val(url);
    }

    function changeManagerURL()
    {
        $("input[@name='manager_url']").val("");
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
                /*
                $.ab_alert(
                    "url_status = " + data.url_status + "<br>" + 
                    "url = " + data.url+ "<br>" + 
                    "cms_type_status = " + data.cms_type_status + "<br>" + 
                    "cms_type_name = " + data.cms_type_name + "<br>" + 
                    "cms_type_version = " + data.cms_type_version + "<br>" + 
                    "manager_url_status = " + data.manager_url_status + "<br>" + 
                    "manager_url = " + data.manager_url + "<br>" + 
                    "manager_html_status = " + data.manager_html_status + "<br>"
                );
                */

                /*
                if(data.url)
                {
                    $("input[@name='input_url']").val(data.url);
                }

                /* url status */

                if(data.url_status == "status_ok") 
                {
                    commitURL();
                }
                else if(data.url_status == "status_failed")
                {
                    changeURL();
                    $.ab_alert("Não foi possível verificar o endereço do CMS informado");
                }
                else if(data.url_status == "status_4xx")
                {
                    changeURL();
                    $.ab_alert("Não foi possível localizar o endereço do CMS informado");
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
                else if(data.cms_type_status == "failed")
                {
                    changeURL();
                    commitCMSType("");
                    $.ab_alert("Não foi possível determinar o tipo de CMS " + 
                               "para o endereço informado");
                }
                else if(data.cms_type_status == "maintenance")
                {
                    commitCMSType("");
                    $.ab_alert("O tipo de CMS " + 
                               data.cms_type_name + " (" + 
                               data.cms_type_version + ") " +
                               " está em manutenção. Tente novamente mais tarde");
                }

                /* manager url */

                if(data.cms_type_status == "ok" &&
                   data.manager_url == "")
                {
                    changeManagerURL();
                }

                if(data.manager_url_status == "status_ok")
                {
                    if(data.manager_html_status == "ok")
                    {
                        commitManagerURL();
                    }
                    else
                    {
                        changeManagerURL();
                        $.ab_alert("O endereço padrão do gerenciador não é válido." +
                                   "Informe o endereço manualmente.");
                    }
                }
                else if(data.manager_url_status == "status_failed")
                {
                    changeManagerURL();
                    $.ab_alert("Não foi possível obter o endereço do gerenciador");
                }
                else if(data.manager_url_status == "status_4xx")
                {
                    changeManagerURL();
                    $.ab_alert("Não foi possível localizar o endereço do gerenciador");
                }
                else if(data.manager_url_status == "status_3xx" || 
                        data.manager_url_status == "status_5xx")
                {
                    changeManagerURL();
                    $.ab_alert("O endereço do gerenciador possui erros ou está" +
                               "sendo redirecionado para outro local");
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
