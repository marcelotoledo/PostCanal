$(document).ready(function()
{
    /* DEFAULTS */
    
    var active_request = false;
    var current_blog = null;
    var current_feed = null;

    /* spinner */

    $.b_spinner
    ({
        image: "<?php B_Helper::img_src('spinner.gif') ?>",
        message: "... <?php echo $this->translation()->application_loading ?>"
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

        _l = 250; /* left bar width */
        _b = 5;   /* container spacing */

        _c = $("#feedscontainer");
        _c.css('top', 0);
        _c.css('left', 0);
        _c.css('width', _l);

        _h = wh - _c.offset().top + _c.height() - _c.outerHeight();

        _c.height(_h);
        maxcontent(_c);

        _c = $("#itemscontainer");
        _c.css('top', 0);
        _c.css('left', _l + _b);

        _w = ww - _c.offset().left + _c.width() - _c.outerWidth();

        _c.width(_w);
        _c.height(_h * 0.5);
        maxcontent(_c);

        _t = _c.offset().top + _c.position().top + _c.height();

        _c = $("#queuecontainer");
        _c.css('top', (_h * 0.5) + _b);
        _c.css('left', _l + _b);
        _c.width(_w);
        _c.height((_h * 0.5) - _b);
        maxcontent(_c);
    }

    maxcontainers();

    /* set default blog */

    <?php if(count($this->blogs) == 1) : ?>
    blog = $("#blogcur").val();
    <?php else : ?>
    blog = $("select[name='bloglst'] > option:selected").val();
    <?php endif ?>

    set_blog(blog);

    <?php endif ?>

    /* SWITCHES */

    /* spinner */

    function sp(b)
    {
        ((active_request = b) == true) ? $.b_spinner_start() : $.b_spinner_stop();
    }

    /* ACTIONS */

    /* error */

    function err()
    {
        alert("<?php echo $this->translation()->server_error ?>");
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

                if(current_feed == null)
                {
                    current_feed = ''; /* TODO */
                }

                feed_item(blog);
            },
            success: function (xml) 
            { 
                d = $(xml).find('data');
            }, 
            error: function () { err(); } 
        });
    }

    /* load feed items */

    function feed_item(blog)
    {
        $.ajax
        ({
            type: "GET",
            url: "<?php B_Helper::url('feed', 'item') ?>",
            dataType: "xml",
            data: { blog: blog, feed: current_feed },
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

    function feed_msg(m)
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
                feed_msg("");
            },
            complete: function()
            {
                sp(false);
            },
            success: function (xml) 
            { 
                d = $(xml).find('data');
                r = d.find('results')

                if(r.length > 0) r = r.children();

                if(r.length > 0)
                {
                    $("#feedaddurlrow").hide();

                    r.each(function()
                    {
                        _node = $(this)[0].nodeName;
                        _url = $(this).find('url').text();
                        _title = $(this).find('title').text();

                        if(_title.length == 0) _title = _url;

                        s = "<input name=\"feedaddoptions[]\" " +
                            "type=\"radio\" node=\"" + _node + "\">" +
                            ( (_title.length > 50) ? 
                              (_title.substring(0, 50) + "...") : 
                              (_title) ) + "<br/>";
                        $("#feedaddoptions > td").append(s);
                    });

                    $("input[name^=feedaddoptions]:first").attr('checked', 'checked');
                }
                else
                {
                    feed_msg("<?php echo $this->translation()->feed_not_found ?>");
                }
            }, 
            error: function () { err(); } 
        });
    }

    function feed_add(node)
    {
        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('feed', 'add') ?>",
            dataType: "xml",
            data: { node: node, blog: current_blog },
            beforeSend: function()
            {
                sp(true);
                feed_msg("");
            },
            complete: function()
            {
                sp(false);
            },
            success: function (xml) 
            { 
                d = $(xml).find('data');
                f = d.find('feed').text();

                if(f.length > 0)
                {
                    $.b_dialog({ selector: "#feedaddform" });
                    $.b_dialog_hide();
                    feed_list(current_blog);
                    current_feed = f;
                }
                else
                {
                    err();
                }
            }, 
            error: function () { err(); } 
        });
    }

    /* set blog (run queue_list > feed_list > feed_item) */

    function set_blog(blog)
    {
        current_blog = blog;
        current_feed = null;
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
        set_blog(blog);
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
            feed_add(feedurl.attr('node'));
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
                feed_msg("<?php echo $this->translation()->blank_url ?>");
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
