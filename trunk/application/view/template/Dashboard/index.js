$(document).ready(function()
{
    var active_request = false;

    var current_blog = null;
    var current_feed = null;

    var feeds_container = $("#feedscontainer");
    var news_container = $("#newscontainer");
    var queue_container = $("#queuecontainer");

    var feeds_content_area = $("#feedscontainer > div.containercontentarea");
    var news_content_area = $("#newscontainer > div.containercontentarea");
    var queue_content_area = $("#queuecontainer > div.containercontentarea");

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

        feeds_container.css('top', 0);
        feeds_container.css('left', 0);
        feeds_container.css('width', _l);

        _h = wh - feeds_container.offset().top + 
                  feeds_container.height() - 
                  feeds_container.outerHeight();

        feeds_container.height(_h);
        maxcontent(feeds_container);

        news_container.css('top', 0);
        news_container.css('left', _l + _b);

        _w = ww - news_container.offset().left + 
                  news_container.width() - 
                  news_container.outerWidth();

        news_container.width(_w);
        news_container.height(_h * 0.5);
        maxcontent(news_container);

        _t = news_container.offset().top + 
             news_container.position().top + 
             news_container.height();

        queue_container.css('top', (_h * 0.5) + _b);
        queue_container.css('left', _l + _b);
        queue_container.width(_w);
        queue_container.height((_h * 0.5) - _b);
        maxcontent(queue_container);
    }

    maxcontainers();

    /* add droppable to queue content area */

    function queuedroppable()
    {
        _c = $("#queuecontainer > div.containercontentarea");
        _c.droppable({
            drop: function(event, ui) {
                add_to_queue(ui.helper.attr('item'));
            },
            scope: 'queue'
        });
    }

    queuedroppable();

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
                queue_populate(d.find('queue').children());
            }, 
            error: function () { err(); } 
        });
    }

    /* load feed list */

    function feed_populate(feeds)
    {
        news_content_area.html("");
        feeds_content_area.html("");

        if(feeds.length > 0)
        {
            feeds.each(function()
            {
                _feed = $(this).find('feed').text();
                _title = $(this).find('title').text();

                _div = "<div class=\"feeditem\" " +
                       "feed=\"" + _feed + "\">" + 
                       _title + "</div>";

                feeds_content_area.append(_div);
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
        news_content_area.html("");

        if(news.length > 0)
        {
            news.each(function()
            {
                news_item = $(this).find('item').text();
                news_date = $(this).find('date').text();
                news_link = $(this).find('link').text();
                news_title = $(this).find('title').text();
                news_author = $(this).find('author').text();
                news_content = $(this).find('content').text();

                if (news_content.match(/\w+/,"") == false)
                {
                    news_content = "<br/>&nbsp;<br/>";
                }

                news_content += "<a href=\"" + news_link + 
                                "\" target=\"_blank\">" + news_link + 
                                "</a>";

                output = "<div class=\"newsitem\" item=\"" + news_item + 
                         "\">" + news_title + 
                         " <i>on " + news_date + 
                         " by " + news_author + 
                         "</i></div><div class=\"newsbody\" item=\"" + news_item + 
                         "\" style=\"display:none\">" + news_content + 
                         "</div>";

                news_content_area.append(output);
            });
        }

        news_content_area.scrollTop(0);

        /* these triggers must be created after populate, otherwise they
         * will not work (because news list are created after document
         * loading */

        $("div.newsitem").click(function()
        {
            set_news_item($(this).attr('item'));
        });

        $("div.newsitem").draggable({
            appendTo: '#queuecontainer > div.containercontentarea',
            axis: 'y',
            containment: 'window',
            helper: 'clone',
            opacity: 0.5,
            scope: 'queue',
            scroll: false
        });
    }

    function set_news_item(item)
    {
        _i = $("div.newsitem[item='" + item + "']");

        if(_i.length > 0)
        {
            if(_i.hasClass('newsitem-selected'))
            {
                _i.removeClass('newsitem-selected');
                $("div.newsbody[item='" + item + "']").hide();
            }
            else
            {
                $("div.newsitem-selected").removeClass('newsitem-selected');
                $("div.newsbody").hide();
                _i.addClass('newsitem-selected');
                $("div.newsbody[item='" + item + "']").show();
            }
            news_content_area.scrollTop(news_content_area.scrollTop() + _i.offset().top - news_content_area.offset().top);
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
            //feed_add(url);
            feed_discover(url);
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
            _url = $(this).find('feed_url').text();
            _title = $(this).find('feed_title').text();
            _description = $(this).find('feed_description').text();

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
                    feed_add(r.find('feed_url').text());
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

    /* add feed item to queue */

    function queue_populate(items)
    {
        queue_content_area.html("");

        if(items.length > 0)
        {
            items.each(function()
            {
                queue_populate_item($(this));
            });
        }

        $("div.queueitem").click(function()
        {
            set_queue_item($(this).attr('item'));
        });
    }

    function queue_populate_item(item)
    {
        queue_item = item.find('item').text();
        queue_title = item.find('item_title').text();
        queue_content = item.find('item_content').text();

        output = "<div class=\"queueitem\" item=\"" + queue_item + 
                 "\">" + queue_title + 
                 "</div><div class=\"queuebody\" item=\"" + queue_item + 
                 "\" style=\"display:none\">" + queue_content + 
                 "</div>";

        queue_content_area.prepend(output);

        $("div.queueitem[item='" + queue_item + "']").click(function()
        {
            set_queue_item($(this).attr('item'));
        });
    }

    function set_queue_item(item)
    {
        _i = $("div.queueitem[item='" + item + "']");

        if(_i.length > 0)
        {
            if(_i.hasClass('queueitem-selected'))
            {
                _i.removeClass('queueitem-selected');
                $("div.queuebody[item='" + item + "']").hide();
            }
            else
            {
                $("div.queueitem-selected").removeClass('queueitem-selected');
                $("div.queuebody").hide();
                _i.addClass('queueitem-selected');
                $("div.queuebody[item='" + item + "']").show();
            }
            //queue_content_area.scrollTop(queue_content_area.scrollTop() + _i.offset().top - queue_content_area.offset().top);
        }
    }

    function add_to_queue(item)
    {
        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('queue', 'add') ?>",
            dataType: "xml",
            data: { item: item, blog: current_blog, feed: current_feed },
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
                queue_populate_item(d.find('result'));
            }, 
            error: function () { err(); } 
        });
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
