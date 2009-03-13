$(document).ready(function()
{
    /* DEFAULTS */
    
    var active_request = false;
    var complete = false;

    defaultSelectCmsItem();

    /* LAYOUT */

    var viewport = 
    {
        v: function() 
        {
            if (self.innerWidth && self.innerHeight) 
            {
                this.pageXOffset = self.pageXOffset;
                this.pageYOffset = self.pageYOffset;
                this.innerWidth = self.innerWidth;
                this.innerHeight = self.innerHeight;
            } 
            else if (document.documentElement && 
                     document.documentElement.clientWidth &&
                     document.documentElement.clientHeight) 
            {
                this.pageXOffset = document.documentElement.scrollLeft;
                this.pageYOffset = document.documentElement.scrollTop;
                this.innerWidth = document.documentElement.clientWidth;
                this.innerHeight = document.documentElement.clientHeight;
            }
            else if (document.body) 
            {
                this.pageXOffset = document.body.scrollLeft;
                this.pageYOffset = document.body.scrollTop;
                this.innerWidth = document.body.clientWidth;
                this.innerHeight = document.body.clientHeight;
            }

            return this;
        },

        initX: function(element, offset, max)
        {
            element.css("height", Math.round((
                viewport.v().innerWidth  - 
                viewport.v().pageXOffset -
                element.position().left  - offset) * max));
        },

        initY: function(element, offset, max) 
        {
            element.css("height", Math.round((
                viewport.v().innerHeight - 
                viewport.v().pageYOffset -
                element.position().top   - offset) * max));
        },

        init: function(element, x, y)
        {
            initX(element, x, 1.0);
            initY(element, y, 1.0);
        }
    };

    viewport.initY($("#mlcb"),    28, 1.0);
    viewport.initY($("#mrcb"),    28, 1.0);
    viewport.initY($("#rbox td"), 28, 0.6);

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
        $(".cmsitm").each(function()
        {
            $(this).removeClass("cmsitm-s");
        });

        $("div[@cid='" + cid + "']").removeClass("cmsitm-h");
        $("div[@cid='" + cid + "']").addClass("cmsitm-s");
    }

    /* FX */

    /* cms */

    $(".cmsitm").hover
    (
        function()
        {
            if($(this).hasClass("cmsitm-s") == false)
                $(this).addClass("cmsitm-h");
        },
        function()
        {
            if($(this).hasClass("cmsitm-s") == false)
                $(this).removeClass("cmsitm-h");
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
        var ls = $(".cmsitm");
        var item = null;
        
        if(ls.size() > 0) selectCmsItem(ls.get(0).getAttribute("cid"));
    }

    /* TRIGGERS */

    /* cms */

    $(".cmsitm").click(function()
    {
        if($(this).hasClass("cmsitm-s") == false)
            selectCmsItem($(this).attr("cid"));
    });
});
