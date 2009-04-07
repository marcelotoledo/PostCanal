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

        _c = $("#newscontainer");
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
    current_blog = $("#blogcur").val();
    <?php elseif(count($this->blog) > 1) : ?>
    current_blog = $("select[name='bloglst'] > option:selected").val();
    <?php endif ?>

    set_blog();

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

    function queue_list()
    {
        $.ajax
        ({
            type: "GET",
            url: "<?php B_Helper::url('queue', 'list') ?>",
            dataType: "xml",
            data: { blog: current_blog },
            beforeSend: function()
            {
                set_active_request(true);
            },
            complete: function()
            {
                set_active_request(false);
                feed_list();
            },
            success: function (xml) 
            { 
                d = $(xml).find('data');
            }, 
            error: function () { err(); } 
        });
    }

    /* load feed list */

    function feed_populate(feeds)
    {
        _c = $("#feedscontainer > div.containercontentarea");
        _c.html("");

        if(feeds.length > 0)
        {
            feeds.each(function()
            {
                _feed = $(this).find('feed').text();
                _title = $(this).find('title').text();

                _div = "<div class=\"feeditem\" " +
                       "feed=\"" + _feed + "\">" + 
                       _title + "</div>";

                _c.append(_div);
            });
        }

        /* this trigger must be created after populate, otherwise
         * will not work (because feed list are created after document
         * loading */

        $("div.feeditem").click(function()
        {
            current_feed = $(this).attr('feed');
            set_feed();
        });
    }

    function set_feed()
    {
        if(current_feed)
        {
            $("div.feeditem-selected").removeClass('feeditem-selected');

            _i = $("div.feeditem[feed='" + current_feed + "']");

            if(_i.length > 0)
            {
                _i.addClass('feeditem-selected');
            }

            /* load feed news */

            feed_news();
        }
    }

    function set_feed_default()
    {
        if(current_feed == null)
        {
            _i = $("div.feeditem:first");

            if(_i.length > 0)
            {
                current_feed = _i.attr('feed');
            }
        }

        set_feed();
    }

    function feed_list()
    {
        $.ajax
        ({
            type: "GET",
            url: "<?php B_Helper::url('feed', 'list') ?>",
            dataType: "xml",
            data: { blog: current_blog },
            beforeSend: function()
            {
                set_active_request(true);
            },
            complete: function()
            {
                set_active_request(false);
                set_feed_default();
            },
            success: function (xml) 
            { 
                d = $(xml).find('data');
                feed_populate(d.find('feeds').children());
            }, 
            error: function () { err(); } 
        });
    }

    /* load feed news */

    function news_populate(news)
    {
        _c = $("#newscontainer > div.containercontentarea");
        _c.html("");

        if(news.length > 0)
        {
            news.each(function()
            {
                _item = $(this).find('item').text();
                _title = $(this).find('title').text();

                _div = "<div class=\"newsitem\" " +
                       "item=\"" + _item + "\">" + 
                       _title + "</div>";

                _c.append(_div);
            });
        }

        /* this trigger must be created after populate, otherwise
         * will not work (because news list are created after document
         * loading */

        $("div.newsitem").click(function()
        {
            set_news_item($(this).attr('item'));
        });
    }

    function set_news_item(item)
    {
        $("div.newsitem-selected").removeClass('newsitem-selected');

        _i = $("div.newsitem[item='" + item + "']");

        if(_i.length > 0)
        {
            _i.addClass('newsitem-selected');
        }
    }

    function feed_news()
    {
        $.ajax
        ({
            type: "GET",
            url: "<?php B_Helper::url('feed', 'news') ?>",
            dataType: "xml",
            data: { blog: current_blog, feed: current_feed },
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
                news_populate(d.find('news').children());
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
        if((url = $("input[name='feedaddoption']:checked").attr('url')) != undefined)
        {
            feed_add(url);
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
            _url = $(this).find('url').text();
            _title = $(this).find('title').text();
            _description = $(this).find('description').text();

            if(_title.length == 0) _title = _url;

            item = "<input name=\"feedaddoption\" " +
                   "type=\"radio\" url=\"" + _url + "\">" +
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
                    feed_add(r.find('url').text());
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

    function feed_add(url)
    {
        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('feed', 'add') ?>",
            dataType: "xml",
            data: { url: url, blog: current_blog },
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
                    feed_list();
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

    /* set blog (run queue_list > feed_list > feed_news) */

    function set_blog()
    {
        current_feed = null;
        feedaddform_hide();
        queue_list();
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
        current_blog = $("select[name='bloglst'] > option:selected").val();
        set_blog();
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
