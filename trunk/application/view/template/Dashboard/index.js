$(document).ready(function()
{
    var active_request = false;

    var top_bar = $("#topbar");
    var current_blog = null;

    var feed_list_area = $("#feedlistarea");
    var feed_display = 'all';

    var bottom_bar = $("#bottombar");
    var queue_list_bar = $("#queuelistbar");
    var queue_display = 0; // 0 none | 1 half | 2 max
    var queue_height_control = $("#bottomrightbar span.hctl");


    /* spinner */

    $.b_spinner
    ({
        image: "<?php B_Helper::img_src('spinner.gif') ?>",
        message: "... <?php echo $this->translation()->application_loading ?>"
    });

    function set_active_request(b)
    {
        ((active_request = b) == true) ? $.b_spinner_start() : $.b_spinner_stop();
    }

    /* window update / maximize containers */

    function window_update()
    {
        ww = $(window).width();
        wh = $(window).height();

        _t_h = top_bar.outerHeight();
        _b_t = bottom_bar.position().top;
        _b_h = bottom_bar.outerHeight();

        if(queue_display == 0)
        {
            bottom_bar.css('top', wh - _b_h);
            queue_height_control.removeClass('hctl-close');
            queue_height_control.addClass('hctl-open');
            queue_list_bar.hide();
        }
        if(queue_display == 1)
        {
            bottom_bar.css('top', wh / 2);
            queue_list_bar.show();
        }
        else if(queue_display == 2)
        {
            bottom_bar.css('top', _t_h);
            queue_height_control.removeClass('hctl-open');
            queue_height_control.addClass('hctl-close');
        }

        _b_t = bottom_bar.position().top;

        feed_list_area.css('top', _t_h);
        feed_list_area.css('left', 0);
        feed_list_area.width(ww);
        feed_list_area.height(wh - _t_h - _b_h);

        if(queue_list_bar.is(':visible'))
        {
            queue_list_bar.css('top', _b_t + _b_h);
            queue_list_bar.css('left', 0);
            queue_list_bar.width(ww);
            queue_list_bar.height(wh - _b_t - _b_h);
        }
    }

    /* error */

    function err()
    {
        alert("<?php echo $this->translation()->server_error ?>");
    }

    /* dashboard */

    function dashboard_msg(m)
    {
        alert(m);
    }

    /* feeds */

    function feed_item_populate(items)
    {
    }

    function feed_populate(feeds)
    {
        if(feeds.length > 0)
        {
            feed_list_area.html("");
            feeds.each(function()
            {
                _feed = $(this).find('feed').text();
                _title = $(this).find('feed_title').text();

                _div = "<div class=\"feeditem\" " +
                       "feed=\"" + _feed + "\">" + _title + "</div>";

                feed_list_area.append(_div);
            });
        }
        else
        {
            feed_list_area.html("<p><?php echo $this->translation()->no_registered_feeds ?></p>");
        }
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
            },
            success: function (xml)
            {
                d = $(xml).find('data');
                feed_populate(d.find('feeds').children());
            },
            error: function () { err(); }
        });
    }

    /* articles */

    function article_populate(articles)
    {
        if(articles.length > 0)
        {
            feed_list_area.html("");
            articles.each(function()
            {
                _article = $(this).find('article').text();
                _title = $(this).find('title').text();
                _link = $(this).find('link').text();
                _date = $(this).find('date').text();
                _feed = null;
                _label = null;

                if(feed_display == 'all')
                {
                    _feed = $(this).find('feed').text();
                }
                else
                {
                    /* void */
                }

                feed_list_area.append("<div class=\"article article" + feed_display + "\" article=\"" + _article + "\"><table><tr><td class=\"articletitle\">" + _title + "</td><td class=\"articledate\">@ " + _date + "</td><td class=\"articlebuttons\"><a href=\"" + _link + "\" target=\"_blank\">[V]</a></td></tr></table></div>");
            });
        }
        else
        {
            feed_list_area.html("<p><?php echo $this->translation()->no_articles ?></p>");
        }
    }

    function article_list()
    {
        $.ajax
        ({
            type: "GET",
            url: "<?php B_Helper::url('article', 'list') ?>",
            dataType: "xml",
            data: { blog: current_blog },
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
                article_populate(d.find('articles').children());
            },
            error: function () { err(); }
        });
    }

    function article_all()
    {
        $.ajax
        ({
            type: "GET",
            url: "<?php B_Helper::url('article', 'all') ?>",
            dataType: "xml",
            data: { blog: current_blog },
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
                article_populate(d.find('articles').children());
            },
            error: function () { err(); }
        });
    }

    /* set feed display mode */

    function set_feed_display(mode)
    {
        $("span.feedsdspall").hide();
        $("span.feedsdspthr").hide();

        if(mode=='all')
        {
            feed_display = mode;
            $("span.feedsdspall").show();
            article_all();
        }
        if(mode=='thr') /* threaded */
        {
            feed_display = mode;
            $("span.feedsdspthr").show();
            feed_list();
        }
    }

    /* set blog */

    function set_blog()
    {
        <?php if(count($this->blogs) == 1) : ?>
        current_blog = $("#blogcur").val();
        <?php elseif(count($this->blogs) > 1) : ?>
        current_blog = $("select[name='bloglst'] > option:selected").val();
        <?php endif ?>

        set_feed_display('all');
    }

    /* EVENTS */

    $(window).resize(function()
    {
        window_update();
    });

    queue_height_control.click(function()
    {
        queue_display = (queue_display < 2) ? queue_display + 1 : 0;
        window_update();
    });
    
    /* blog selector */

    $("select[name='bloglst']").change(function()
    {
        set_blog();
    });

    /* feed display */

    $("a#feeddsplnkall").click(function()
    {
        set_feed_display('all');
    });

    $("a#feeddsplnkthr").click(function()
    {
        set_feed_display('thr');
    });

    /* INIT */

    window_update();

    <?php if(count($this->blogs) > 0) : ?>

    set_blog();

    <?php else : ?>

    $.b_dialog({ selector: "#noblogmsg", modal: true });
    $.b_dialog_show();

    <?php endif ?>
});
