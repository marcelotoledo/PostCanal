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

    function feed_discover_msg(m)
    {
        _f  = $("#feedaddmessage");
        _td = $("#feedaddmessage td");

        if(m=="")
        {
            _td.html("");
            _f.hide();
        }
        else
        {
            _td.html(m);
            _f.show();
        }
    }

    function feed_show_options(l)
    {
        $("#feedaddurlrow").hide();

        l.each(function()
        {
            i = $(this).text();
            j = i.length;
            s = (j > 50) ? (i.substring(0, 25) + "..." + i.substring(j - 25)) : (i); 
            $("#feedaddoptions > td").append("<input name=\"feedaddoptions[]\" type=\"radio\" value=\"" + i + "\">" + s + "<br/>");
        });

        $("input[name^=feedaddoptions]:first").attr('checked', 'checked');
    }

    function feed_discover(url)
    {
        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('feed', 'discover') ?>",
            dataType: "xml",
            data: { url: url },
            beforeSend: function()
            {
                sp(true);
                feed_discover_msg("");
            },
            complete: function()
            {
                sp(false);
            },
            success: function (xml) 
            { 
                d = $(xml).find('data');
                r = d.find('results')

                if(r.length > 0)
                {
                    r.each(function()
                    {
                        feed_show_options($(this));
                    });
                }
                else
                {
                    feed_discover_msg("<?php echo $this->translation->feed_not_found ?>");
                }
            }, 
            error: function () { err(); } 
        });
    }

    function feed_add(url)
    {
        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('feed', 'add') ?>",
            dataType: "xml",
            data: { url: url },
            beforeSend: function()
            {
                sp(true);
                feed_discover_msg("");
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

    /* set blog (run queue_list > feed_list > feed_item) */

    function setblog(blog)
    {
        queue_list(blog);
    }

    /* TRIGGERS */

    /* reload when window resizes */

    $(window).resize(function()
    {
        maxcontainers();
    });

    /* disable form submit */

    $("select[name='bloglst']").change(function()
    {
        blog = $("select[name='bloglst'] > option:selected").val();
        setblog(blog);
    });

    $("#feedaddlnk").click(function()
    {
        $.b_dialog({ selector: "#feedaddform" });
        $.b_dialog_show();
        $("#feedaddurlrow").show();
        $("#feedaddoptions > td").html("");
        $("input[name='feedaddurl']").focus();
    });

    $("input[name='feedaddsubmit']").click(function()
    {
        feedurl = $("input[name^=feedaddoptions]:checked");

        if(feedurl.val() != undefined)
        {
            feed_add(feedurl.val());
        }
        else
        {
            $("#feedaddurlrow").show();
            $("#feedaddoptions > td").html("");
            url = $("input[name='feedaddurl']").val();

            if(url)
            {
                feed_discover(url);
            }
            else
            {
                feed_discover_msg("<?php echo $this->translation->blank_url ?>");
            }
        }
    });

    $("input[name='feedaddurl']").keypress(function(e) 
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            $("input[name='feedaddsubmit']").click();
        }
    });
});
