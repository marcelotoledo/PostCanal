$(document).ready(function()
{
    var active_request = false;

    var body__ = $("body");

    var top_bar = $("#topbar");
    var blog_select_list = $("select[name='bloglst']");
    var current_blog = null;

    var feed_list_area = $("#feedlistarea");
    var feed_display = "<?php echo $this->profile_preference['dashboard_feed_display'] ?>";
    var feed_navigation = $("div.feednavigation");

    var article_display = "<?php echo $this->profile_preference['dashboard_article_display'] ?>";
    var articles_content = Array();
    var current_article = null;


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

        _t_h = top_bar.outerHeight() + $("div#feedareahead").outerHeight();

        feed_list_area.css('top', _t_h);
        feed_list_area.css('left', 0);
        feed_list_area.width(ww);
        feed_list_area.height(wh - _t_h);

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
            data: { blog: current_blog, enabled: true },
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

    function article_scroll(article)
    {
        b = $("div.article[article='" + article + "']");
        _fa_p = feed_list_area.position().top;
        _fa_s = feed_list_area.scrollTop();
        _ah_p = b.position().top;
        _ah_s = _fa_s + _ah_p - _fa_p;
        feed_list_area.scrollTop(_ah_s);
    }

    function article_content_show(article)
    {
        if(articles_content[article])
        {
            current_article = article;
            a = articles_content[article];
            b = $("div.article[article='" + article + "']");
            c = $("div.articlecontent[article='" + article + "']"); 
            d = "";

            if(a.content)
            {
                if(a.title)
                {
                    d += "<h1>" + a.title + "</h1>";
                }
                if(a.author)
                {
                    d += "<h2>" + a.author + "</h2>";
                }

                d += "<p>" + a.content + "</p>\n";
            }
            else
            {
                d += "<?php echo $this->translation()->no_content ?>\n";
            }

            c.html(d);
            b.addClass('articlecontentshow'); 
            c.show();

            /* scroll */

            if(article_display == 'lst')
            {
                article_scroll(article);
            }
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
            $(this).hide(); // hide title
        });
    }

    function article_content_hide(article)
    {
        b = $("div.article[article='" + article + "']"); 
        c = $("div.articlecontent[article='" + article + "']"); 
        c.html("");
        c.hide();
        b.removeClass('articlecontentshow'); 
        b.show(); // when hide
    }

    function article_content_hide_all()
    {
        feed_list_area.find("div.articlecontentshow").each(function()
        {
            article = $(this).attr('article');
            article_content_hide(article);
        });
    }

    function article_populate(articles, container)
    {
        if(articles.length > 0)
        {
            container.html("");
            _counter = 0;
            articles.each(function()
            {
                _counter++;
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
                container.append("<div class=\"article article" + feed_display + "\" article=\"" + _article + "\" counter=\"" + _counter + "\" lastitem=\"" + ((_counter == articles.length) ? 'true' : 'false') + "\"><div class=\"articlelabel\"><nobr>" + _label + "</nobr></div><div class=\"articleinfo\">@ " + _info + "</div><div class=\"articlebuttons\">" + _buttons + "</div><div style=\"clear:both\"></div></div><div class=\"articlecontent\" article=\"" + _article + "\"></div>\n");
                articles_content[_article] = { 
                    title:   _title,
                    author:  $(this).find('author').text(),
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
            if(article_display == 'lst')
            {
                _a = $(this).attr('article');

                if($(this).hasClass('articlecontentshow'))
                {
                    article_content_hide(_a);
                }
                else
                {
                    article_content_hide_all();
                    article_content_show(_a);
                }
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

    function article_refresh()
    {
        if(feed_display=='all')
        {
            article_all();
        }
        if(feed_display=='thr') /* threaded */
        {
            feed_list();
        }

        feed_list_area.scrollTop(0);
    }

    function article_next()
    {
        if(current_article==null)
        {
            _c_a = feed_list_area.find("div.article");

            if(k = _c_a.attr('article'))
            {
                article_content_show(k);
            }
        }
        else
        {
            _c_a = $("div.article[article='" + current_article + "']");
            _n_a = _c_a.nextAll("div.article");

               j = _c_a.attr('article');
            if(k = _n_a.attr('article'))
            {
                article_content_hide(j)
                article_content_show(k);
            }
        }
    }

    function article_previous()
    {
        if(current_article)
        {
            _c_a = $("div.article[article='" + current_article + "']");
            _p_a = _c_a.prevAll("div.article");

               j = _c_a.attr('article');
            if(k = _p_a.attr('article'))
            {
                article_content_hide(j)
                article_content_show(k);
            }
        }
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
            feed_navigation.show();
            article_content_hide_all();
        }
        if(mode=='exp') /* expanded */
        {
            article_display = mode;
            $("span.articledspexp").show();
            feed_navigation.hide();
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
        feed_list_area.scrollTop(0);
    }

    /* preference */

    function get_preference(k)
    {
        return do_preference(k, null);
    }

    function set_preference(k, v)
    {
        do_preference(k, v);
    }

    function do_preference(k, v)
    {
        $.ajax
        ({
            type: (v ? "POST" : "GET"),
            url: "<?php B_Helper::url('profile', 'preference') ?>",
            dataType: "xml",
            data: { k: k, v: v },
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
                k = d.find('k')
                v = d.find('v')
            },
            error: function () { err(); }
        });

        return v;
    }

    /* set blog */

    function set_blog()
    {
        <?php if(count($this->blogs) == 1) : ?>
        current_blog = $("#blogcur").val();
        <?php elseif(count($this->blogs) > 1) : ?>
        current_blog = blog_select_list.find("option:selected").val();
        <?php endif ?>
        set_feed_display(feed_display);
    }

    /* TRIGGERS */

    $(window).resize(function()
    {
        window_update();
    });

    /* blog selector */

    blog_select_list.change(function()
    {
        set_blog();
        set_preference('dashboard_current_blog', current_blog);
    });

    /* feed display */

    $("a#feeddsplnkall").click(function()
    {
        set_feed_display('all');
        set_preference('dashboard_feed_display', feed_display);
    });

    $("a#feeddsplnkthr").click(function()
    {
        set_feed_display('thr');
        set_preference('dashboard_feed_display', feed_display);
    });

    /* article display */

    $("a#articledsplnklst").click(function()
    {
        set_article_display('lst');
        set_preference('dashboard_article_display', article_display);
    });

    $("a#articledsplnkexp").click(function()
    {
        set_article_display('exp');
        set_preference('dashboard_article_display', article_display);
    });

    $("a#feedrefreshlnk").click(function()
    {
        article_refresh();
    });

    $("a#articlepreviouslnk").click(function()
    {
        article_previous();
    });

    $("a#articlenextlnk").click(function()
    {
        article_next();
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
