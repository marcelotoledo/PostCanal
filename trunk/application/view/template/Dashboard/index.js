$(document).ready(function()
{
    /* DEFAULTS */
    
    var active_request = false;
    var complete = false;

    defaultSelectCmsItem();


    /* SWITCHES */

    /* spinner */

    function showSpinner()
    {
        $.ab_spinner
        ({
            height: 32, width: 32,
            image: "<?php B_Helper::img_src('spinner/linux_spinner.png') ?>",
            message: "... carregando"
        });
    }

    function hideSpinner()
    {
        $.ab_spinner_stop();
    }

    /* cms */        

    function setSelectedCmsItem(cid)
    {
        $(".cms-item").each(function()
        {
            $(this).removeClass("cms-item-selected");
        });

        $("div[@cid='" + cid + "']").addClass("cms-item-selected");
    }

    /* FX */

    /* cms */

    $(".cms-item").hover
    (
        function()
        {
            if($(this).hasClass("cms-item-selected") == false)
                $(this).addClass("cms-item-hover");
        },
        function()
        {
            if($(this).hasClass("cms-item-selected") == false)
                $(this).removeClass("cms-item-hover");
        }
    );

    /* ACTIONS */

    /* cms */

    function onErrorCmsItem()
    {
        alert("erro ao carregar o cms");
    }

    function loadCmsItem(cid)
    {
        if(active_request == true)
        {
            return null;
        }

        var parameters = { cid: cid }

        $.ajax
        ({
            type: "GET",
            url: "<?php B_Helper::url('dashboard', 'cms') ?>",
            dataType: "html",
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
                $("#right-panel").html(data);
            }, 
            error: function () { onErrorCmsItem(); }
        });
    }

    function selectCmsItem(cid)
    {
        setSelectedCmsItem(cid);
        loadCmsItem(cid);
    }

    function defaultSelectCmsItem()
    {
        var ls = $(".cms-item");
        var item = null;
        
        if(ls.size() > 0) selectCmsItem(ls.get(0).getAttribute("cid"));
    }

    /* TRIGGERS */

    /* cms */

    $(".cms-item").click(function()
    {
        if($(this).hasClass("cms-item-selected") == false)
            selectCmsItem($(this).attr("cid"));
    });
});
