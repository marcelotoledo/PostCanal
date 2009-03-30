$(document).ready(function()
{
    /* DEFAULTS */
    
    var ar = false;

    /* spinner */

    $.b_spinner
    ({
        image: "<?php B_Helper::img_src('spinner.gif') ?>",
        message: "... <?php echo $this->translation->application_loading ?>"
    });

    /* CONTAINERS */

    <?php if(count($this->blogs) == 0) : ?>

    $.b_dialog({ selector: "#noblogmsg", modal: true });
    $.b_dialog_show();

    <?php else : ?>

    /* maximize content area */

    function maxcontent(o)
    {
        _c = $("#" + o.attr('id') + " > div.containercontentarea");
        _f = $("#" + o.attr('id') + " > div.containerfooter").height();

        if(_c.position())
        { 
            _c.height((o.height() - _c.position().top) - _f);
        }
    }

    /* maximize containers */

    function maxcontainers()
    {
        ww = $(window).width();
        wh = $(window).height();

        _c = $("#feedscontainer");
        _h = wh - _c.offset().top + _c.height() - _c.outerHeight();
        _c.height(_h);
        maxcontent(_c);

        _c = $("#itemscontainer");
        _w = ww - _c.offset().left + _c.width() - _c.outerWidth();
        _c.width(_w);
        _c.height(_h * 0.5);
        maxcontent(_c);

        _t = _c.offset().top + _c.position().top + _c.height();

        _c = $("#queuecontainer");
        _c.width(_w);
        _c.css('top', _t - 8);
        _c.height((_h * 0.5) - 5);
        maxcontent(_c);
    }

    maxcontainers();

    /* set default blog */

    <?php if(count($this->blogs) == 1) : ?>
    blog = $("#blogcur").val();
    <?php else : ?>
    blog = $("select[name='bloglst'] > option:selected").val();
    <?php endif ?>

    setblog(blog);

    <?php endif ?>

    /* SWITCHES */

    /* spinner */

    function sp(b)
    {
        ((ar = b) == true) ? $.b_spinner_start() : $.b_spinner_stop();
    }

    /* ACTIONS */

    /* error */

    function err()
    {
        alert("<?php echo $this->translation->server_error ?>");
    }

    /* load queue list */

    function queue_list(blog)
    {
        $.ajax
        ({
            type: "GET",
            url: "<?php B_Helper::url('queue', 'list') ?>",
            dataType: "xml",
            data: { blog: blog },
            beforeSend: function()
            {
                sp(true);
            },
            complete: function()
            {
                feed_list(blog);
            },
            success: function (xml) 
            { 
                d = $(xml).find('data');
            }, 
            error: function () { err(); } 
        });
    }

    /* load feed list */

    function feed_list(blog)
    {
        $.ajax
        ({
            type: "GET",
            url: "<?php B_Helper::url('feed', 'list') ?>",
            dataType: "xml",
            data: { blog: blog },
            beforeSend: function()
            {
                /* void */
            },
            complete: function()
            {
                /* load items from current feed */

                feed = ''; /* TODO */

                feed_item(blog, feed);
            },
            success: function (xml) 
            { 
                d = $(xml).find('data');
            }, 
            error: function () { err(); } 
        });
    }

    /* load feed items */

    function feed_item(blog, feed)
    {
        $.ajax
        ({
            type: "GET",
            url: "<?php B_Helper::url('feed', 'item') ?>",
            dataType: "xml",
            data: { blog: blog, feed: feed},
            beforeSend: function()
            {
                /* void */
            },
            complete: function()
            {
                sp(false);
            },
            success: function (xml) 
            { 
                d = $(xml).find('data');
            }, 
            error: function () { err(); } 
        });
    }

    /* feed add */

    function feed_discover()
    {
        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('feed', 'discover') ?>",
            dataType: "xml",
            data: { url: $("input[name='feedaddurl']").val() },
            beforeSend: function()
            {
                sp(true);
            },
            complete: function()
            {
                sp(false);
            },
            success: function (xml) 
            { 
                d = $(xml).find('data');
                d.find('results').each(function()
                {
                    $(this).each(function()
                    {
                        alert($(this).text()); // ... TODO
                    });
                });
            }, 
            error: function () { err(); } 
        });
    }

    /* set blog 
     * queue_list > feed_list > feed_item */

    function setblog(blog)
    {
        queue_list(blog);
    }

    /* TRIGGERS */

    $("select[name='bloglst']").change(function()
    {
        blog = $("select[name='bloglst'] > option:selected").val();
        setblog(blog);
    });

    $("#feedaddlnk").click(function()
    {
        $.b_dialog({ selector: "#feedaddform" });
        $.b_dialog_show();
        $("input[name='feedaddurl']").focus();
    });

    $("input[name='feedaddsubmit']").click(function()
    {
        url = $("input[name='feedaddurl']").val();
        feed_discover(url)
    });
});
