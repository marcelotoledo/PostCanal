$(document).ready(function()
{
    var active_request = false;

    var top_bar = $("#topbar");
    var current_blog = null;

    var feed_list_area = $("#feedlistarea");
    var feed_display = '<?php echo $this->feed_display ?>';

    var article_display = '<?php echo $this->article_display ?>';
    var articles_content = Array();

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

        feed_list_area.find('div.articlelabel').width(ww * 0.6);
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

    function feed_populate(feeds)
    {
        if(feeds.length > 0)
        {
            feed_list_area.html("");
            feeds.each(function()
            {
                _feed = $(this).find('feed').text();
                _title = $(this).find('feed_title').text();
                feed_list_area.append("<div class=\"feeditem\" feed=\"" + _feed + "\">" + _title + "</div><div class=\"feeditemarticles\" feed=\"" + _feed + "\"></div>\n");
            });
        }
        else
        {
            feed_list_area.html("<p><?php echo $this->translation()->no_registered_feeds ?></p>");
        }

        /* this trigger must be created after populate, otherwise
         * will not work (because populate write elements after document loading */

        $("div.feeditem").click(function()
        {
            feed = $(this).attr('feed');

            if($(this).hasClass('feeditemthreaded'))
            {
                article_unthread(feed);
            }
            else
            {
                article_thread(feed);
            }
        });
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

    function article_content_show(article)
    {
        if(articles_content[article])
        {
            a = articles_content[article];
            c = $("div.articlecontent[article='" + article + "']"); 
            d = "";
            /*
            if(a.author)
            {
                d += "<p><b><?php echo $this->translation()->author ?>: </b>" + a.author + "</p>";
            }
            */
            if(a.content)
            {
                d += "<p>" + a.content + "</p>";
            }
            if(d == "")
            {
                d = "<?php echo $this->translation()->no_content ?>";
            }
            if(a.title)
            {
                d = "<h1>" + a.title + "</h1>\n" + d;
            }
            c.html(d);
            c.show();
            $("div.article[article='" + article + "']").addClass('articlecontentshow'); 
        }
    }

    function article_content_show_all()
    {
        feed_list_area.find("div.article").each(function()
        {
            if($(this).hasClass('articlecontentshow') == false)
            {
                article = $(this).attr('article');
                article_content_show(article);
            }
        });
    }

    function article_content_hide(article)
    {
        c = $("div.articlecontent[article='" + article + "']"); 
        c.html("");
        c.hide();
        $("div.article[article='" + article + "']").removeClass('articlecontentshow'); 
    }

    function article_content_hide_all()
    {
        feed_list_area.find("div.articlecontentshow").each(function(){
            article = $(this).attr('article');
            article_content_hide(article);
        });
    }

    function article_populate(articles, container)
    {
        if(articles.length > 0)
        {
            container.html("");
            articles.each(function()
            {
                _article = $(this).find('article').text();
                _title = $(this).find('title').text();
                _link = $(this).find('link').text();
                _date = $(this).find('date').text();
                _label = "";
                if(feed_display == 'all')
                {
                    _feed = $(this).find('feed').text();
                    _label += "<div class=\"articlefeed\">" + _feed + "</div>";
                }
                _label+= "<div class=\"articletitle\">" + _title + "</div>";
                _info = _date;
                _buttons = "<a href=\"" + _link + "\" target=\"_blank\">view</a>";
                container.append("<div class=\"article article" + feed_display + "\" article=\"" + _article + "\"><div class=\"articlelabel\"><nobr>" + _label + "</nobr></div><div class=\"articleinfo\">@ " + _info + "</div><div class=\"articlebuttons\">" + _buttons + "</div><div style=\"clear:both\"></div></div><div class=\"articlecontent\" article=\"" + _article + "\"></div>\n");
                articles_content[_article] = { 
                    title:   _title,
                    /* author:  $(this).find('author').text(), */
                    content: $(this).find('content').text() 
                };
            });
        }
        else
        {
            container.html("<p><?php echo $this->translation()->no_articles ?></p>");
        }

        /* this trigger must be created after populate, otherwise
         * will not work (because populate write elements after document loading */

        container.find("div.article").click(function()
        {
            article = $(this).attr('article');

            if($(this).hasClass('articlecontentshow'))
            {
                article_content_hide(article);
            }
            else
            {
                article_content_show(article);
            }
        });

        window_update();

        if(article_display == 'exp')
        {
            article_content_show_all();
        }
    }

    function article_thread(feed)
    {
        c = $("div.feeditemarticles[feed='" + feed + "']"); 
        article_threaded(feed, c);
        c.show();
        c = $("div.feeditem[feed='" + feed + "']"); 
        c.addClass('feeditemthreaded');
    }

    function article_unthread(feed)
    {
        c = $("div.feeditemarticles[feed='" + feed + "']"); 
        c.html("");
        c.hide();
        c = $("div.feeditem[feed='" + feed + "']"); 
        c.removeClass('feeditemthreaded');
    }

    function article_threaded(feed, container)
    {
        $.ajax
        ({
            type: "GET",
            url: "<?php B_Helper::url('article', 'threaded') ?>",
            dataType: "xml",
            data: { blog: current_blog, feed: feed },
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
                article_populate(d.find('articles').children(), container);
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
                article_populate(d.find('articles').children(), feed_list_area);
            },
            error: function () { err(); }
        });
    }

    /* set article display mode */

    function set_article_display(mode)
    {
        $("span.articledsplst").hide();
        $("span.articledspexp").hide();

        if(mode=='lst') /* list */
        {
            article_display = mode;
            $("span.articledsplst").show();
            article_content_hide_all();
        }
        if(mode=='exp') /* expanded */
        {
            article_display = mode;
            $("span.articledspexp").show();
            article_content_show_all();
        }
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

        set_article_display(article_display);
    }

    /* set blog */

    function set_blog()
    {
        <?php if(count($this->blogs) == 1) : ?>
        current_blog = $("#blogcur").val();
        <?php elseif(count($this->blogs) > 1) : ?>
        current_blog = $("select[name='bloglst'] > option:selected").val();
        <?php endif ?>

        set_feed_display(feed_display);
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

    /* article display */

    $("a#articledsplnklst").click(function()
    {
        set_article_display('lst');
    });

    $("a#articledsplnkexp").click(function()
    {
        set_article_display('exp');
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
