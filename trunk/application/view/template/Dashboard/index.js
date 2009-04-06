$(document).ready(function()
{
    /* DEFAULTS */
    
    var active_request = false;

    var window_width = 0;
    var window_height = 0;

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

    function scrollbarSize()
    {
        _d = $("<div style=\"width:50px;height:50px;overflow:hidden;position:absolute;top:-200px;left:-200px;\"><div style=\"height:100px;\"></div>");
        $("body").append(_d);
        _a = $("div", _d).innerWidth();
        _d.css("overflow-y", "scroll");
        _b = $("div", _d).innerWidth();
        $(_d).remove();
        return _a - _b;
    }

    function maxcontainers()
    {
        sb = scrollbarSize();

        ww = $(window).width();
        wh = $(window).height();

        if(ww < window_width) { ww += sb; }
        if(wh < window_height) { wh += sb; }

        window_width = ww;
        window_height = wh;

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

    function set_active_request(b)
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
                set_active_request(true);
            },
            complete: function()
            {
                set_active_request(false);
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

    function feed_list_populate(feeds)
    {
        _c = $("#feedscontainer > div.containercontentarea");
        _c.html("");

        if(feeds.length > 0)
        {
            feeds.each(function()
            {
                _feed = $(this).find('feed').text();
                _title = $(this).find('title').text();

                _item = "<div class=\"feeditem\" " +
                        "feed=\"" + _feed + "\">" + 
                        _title + "</div>";

                _c.append(_item);
            });
        }
    }

    function set_feed_auto()
    {
        if(current_feed == null)
        {
            _i = $("#feedscontainer > " +
                   "div.containercontentarea > " +
                   "div.feeditem:first");

            if(_i.length > 0)
            {
                current_feed = _i.attr('feed');
            }
        }

        _i = $("div[feed='" + current_feed + "']");

        if(_i.length > 0)
        {
            _i.addClass('feeditem-selected');
        }
    }

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
                set_active_request(true);
            },
            complete: function()
            {
                set_active_request(false);
                set_feed_auto();
                feed_item(blog);
            },
            success: function (xml) 
            { 
                d = $(xml).find('data');
                feed_list_populate(d.find('feeds').children());
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
                set_active_request(true);
            },
            complete: function()
            {
                set_active_request(false);
            },
            success: function (xml) 
            { 
                d = $(xml).find('data');
            }, 
            error: function () { err(); } 
        });
    }

    /* feed add */

    function feedaddform_reset()
    {
        $("#feedaddoptions > td").html("");
        $("input[name='feedaddurl']").val("");
        $("#feedaddurlrow").show();
    }

    function feedaddform_show()
    {
        $.b_dialog({ selector: "#feedaddform" });
        $.b_dialog_show();
        feedaddform_reset();
        $("input[name='feedaddurl']").focus();
    }

    function feedaddform_hide()
    {
        $.b_dialog({ selector: "#feedaddform" });
        $.b_dialog_hide();
    }

    function feedaddform_submit()
    {
        if((k = $("input[name='feedaddoption']:checked").attr('key')) != undefined)
        {
            feed_add(k);
        }
        else 
        {
            if((url = $("input[name='feedaddurl']").val()) != "")
            {
                feed_discover(url);
            }
            else
            {
                feed_msg("<?php echo $this->translation()->blank_url ?>");
            }
        }
    }

    function feedaddform_options(feeds)
    {
        $("#feedaddurlrow").hide();

        feeds.each(function()
        {
            _key = $(this).attr('key');
            _url = $(this).find('url').text();
            _title = $(this).find('title').text();

            if(_title.length == 0) _title = _url;

            item = "<input name=\"feedaddoption\" " +
                   "type=\"radio\" key=\"" + _key + "\">" +
                   ( (_title.length > 50) ? 
                     (_title.substring(0, 50) + "...") : 
                     (_title) ) + "<br/>";
            $("#feedaddoptions > td").append(item);
        });

        $("input[name='feedaddoption']:first").attr('checked', 'checked');
    }

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
                set_active_request(true);
                feed_msg("");
            },
            complete: function()
            {
                set_active_request(false);
            },
            success: function (xml) 
            { 
                d = $(xml).find('data');
                r = d.find('results')

                if(r.length > 0) r = r.children();

                if(r.length == 1)
                {
                    feed_add(r.attr('key'));
                }
                else if(r.length >  1)
                {
                    feedaddform_options(r);
                }
                else
                {
                    feed_msg("<?php echo $this->translation()->feed_not_found ?>");
                }
            }, 
            error: function () { err(); } 
        });
    }

    function feed_add(key)
    {
        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('feed', 'add') ?>",
            dataType: "xml",
            data: { key: key, blog: current_blog },
            beforeSend: function()
            {
                set_active_request(true);
                feed_msg("");
            },
            complete: function()
            {
                set_active_request(false);
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
        feedaddform_hide();
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
        feedaddform_show();
    });

    $("input[name='feedaddsubmit']").click(function()
    {
        feedaddform_submit();
    });

    $("input[name='feedaddurl']").keypress(function(e) 
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            $("input[name='feedaddsubmit']").click();
        }
    });
});
