$(document).ready(function()
{
    var active_request = false;

    var ww = 0;
    var wh = 0;

    var magic_q_min = 5;
    var magic_q_exp = 16;
    var magic_q_max = 140;

    var body__ = $("body");

    var top_bar = $("div#topbar");
    var blog_select_list = $("select[name='bloglst']");
    var current_blog = null;

    var feed_area = $("div#feedarea");
    var feed_list_area = feed_area.find("div#feedlistarea");
    var feed_display = 'all';

    var article_display = 'lst';
    var articles_content = Array();
    var current_article = null;

    var queue_area = $("div#queuearea");
    var queue_list_area = queue_area.find("div#queuelistarea");
    var queue_hctrl_display = 0 // 0 min | 1 exp | 2 max

    var queue_mode = 'manual';
    var queue_running = 'no';
    var queue_spawning = 3600;


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

    /* visual updates */

    function feed_area_enable()
    {
        feed_area.find("div#feedareahead").removeClass('areadisabled');
        feed_area.find("div.feeddisplay").show();
        feed_area.find("div.articledisplay").show();
        if(article_display=='lst') // @see set_article_display()
        {
            feed_area.find("div.feednavigation").show();
        }
        feed_area.find("div.feedrefresh").show();
    }

    function feed_area_disable()
    {
        feed_area.find("div#feedareahead").addClass('areadisabled');
        feed_area.find("div.feeddisplay").hide();
        feed_area.find("div.articledisplay").hide();
        feed_area.find("div.feednavigation").hide();
        feed_area.find("div.feedrefresh").hide();
    }

    function queue_minimize()
    {
        queue_area.css('bottom', 0);
        queue_list_area.hide();
        feed_area_enable();
        feed_list_area.show();
        queue_area.find("a.queuehctrllnk").hide();
        queue_area.find("a#queuehctrlexp").show();
    }

    function queue_expand(h)
    {
        queue_area.css('bottom', h - queue_area.outerHeight() - magic_q_exp);
        queue_list_area.show();
        queue_area.find("a.queuehctrllnk").hide();
        queue_area.find("a#queuehctrlmax").show();
    }

    function queue_maximize()
    {
        queue_area.css('bottom', wh - magic_q_max);
        feed_list_area.hide();
        feed_area_disable();
        queue_area.find("a.queuehctrllnk").hide();
        queue_area.find("a#queuehctrlmin").show();
    }

    function window_update()
    {
        ww = $(window).width();
        wh = $(window).height();

        _t_h = top_bar.outerHeight() + feed_area.find("div#feedareahead").outerHeight();
        _b_h = 0;

        if(queue_hctrl_display == 0)
        {
            _b_h = queue_area.find("div#queueareahead").outerHeight() + magic_q_min;
            queue_minimize();
        }
        if(queue_hctrl_display == 1)
        {
            _b_h = wh / 2;
            queue_expand(_b_h);
        }
        if(queue_hctrl_display == 2)
        {
            _b_h = wh;
            queue_maximize();
        }

        feed_list_area.css('top', _t_h);
        feed_list_area.css('left', 0);
        feed_list_area.width(ww);
        feed_list_area.height(wh - _t_h - _b_h);

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
            feed_list_area.html("<br/><span><?php echo $this->translation()->no_registered_feeds ?></span>");
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

    function article_populate(articles, container, append)
    {
        if(articles.length==0)
        {
            if(append==true)
            {
                if((m = container.find("div.articlemore"))) { m.remove(); }
            }
            else
            {
                container.html("<br/><span><?php echo $this->translation()->no_articles ?></span>");
            }

            return null;
        }

        if(append==false)
        {
            container.html("");
        }
        else
        {
            if((m = container.find("div.articlemore"))) { m.remove(); }
        }
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
            container.append("<div class=\"article article" + feed_display + "\" article=\"" + _article + "\" bound=\"no\"><div class=\"articlelabel\"><nobr>" + _label + "</nobr></div><div class=\"articleinfo\">@ " + _info + "</div><div class=\"articlebuttons\">" + _buttons + "</div><div style=\"clear:both\"></div></div><div class=\"articlecontent\" article=\"" + _article + "\"></div>\n");
            articles_content[_article] = { 
                title:   _title,
                author:  $(this).find('author').text(),
                content: $(this).find('content').text() 
            };
        });
        container.append("<div class=\"articlemore\" older=\"" + _date + "\"><center><?php echo $this->translation()->older ?></center></div>");

        /* article triggers must be created after populate, otherwise
         * will not work (because populate write elements after document loading */

        container.find("div.article[bound='no']").each(function()
        {
            $(this).attr('bound', 'yes');
            $(this).click(function()
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
        });

        container.find("div.articlemore").click(function()
        {
            article_more(container, $(this).attr('older'));
        });

        window_update();

        if(article_display == 'exp')
        {
            article_content_show_all();
        }

        body__.trigger('article_next_evt');
        body__.unbind('article_next_evt');
    }

    function article_thread(feed)
    {
        c = feed_list_area.find("div.feeditemarticles[feed='" + feed + "']"); 
        article_list(c);
        c.show();
        c = feed_list_area.find("div.feeditem[feed='" + feed + "']"); 
        c.addClass('feeditemthreaded');
    }

    function article_unthread(feed)
    {
        c = feed_list_area.find("div.feeditemarticles[feed='" + feed + "']"); 
        c.html("");
        c.hide();
        c = feed_list_area.find("div.feeditem[feed='" + feed + "']"); 
        c.removeClass('feeditemthreaded');
    }

    function __article_load(container, append, older)
    {
        url = (feed_display == 'thr') ? 
                    "<?php B_Helper::url('article', 'threaded') ?>" : 
                    "<?php B_Helper::url('article', 'all') ?>";
        $.ajax
        ({
            type: "GET",
            url: url,
            dataType: "xml",
            data: { blog: current_blog, feed: container.attr('feed'), older: older },
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
                article_populate(d.find('articles').children(), container, append);
            },
            error: function () { err(); }
        });
    }

    function article_list(container)
    {
        __article_load(container, false, null);
    }

    function article_more(container, older)
    {
        __article_load(container, true, older);
    }

    function article_refresh()
    {
        if(feed_display=='all')
        {
            article_list(feed_list_area);
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
            else // try to load older articles
            {
                _c = (feed_display=='thr') ? _c_a.parents() : feed_list_area;
                if((_m = _c.find("div.articlemore")))
                {
                    _m.click();
                    body__.bind('article_next_evt', function(e) { article_next(); });
                }
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
            feed_area.find("div.feednavigation").show();
            article_content_hide_all();
        }
        if(mode=='exp') /* expanded */
        {
            article_display = mode;
            $("span.articledspexp").show();
            feed_area.find("div.feednavigation").hide();
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
            article_list(feed_list_area);
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

    function load_preference(t)
    {
        url = null
        if(t=='profile') { url = "<?php B_Helper::url('profile', 'preference') ?>"; }
        if(t=='blog')    { url = "<?php B_Helper::url('blog',    'preference') ?>"; }

        $.ajax
        ({
            type: "GET",
            url: url,
            dataType: "xml",
            data: { },
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
                p = d.find('preference');

                if(t=='profile')
                {
                    set_blog(p.find('current_blog').text());
                }
                if(t=='blog')
                {
                    queue_set_mode(p.find('queue_mode').text());
                }
            },
            error: function () { err(); }
        });
    }

    function save_preference(t, k, v)
    {
        url = null
        if(t=='profile') { url = "<?php B_Helper::url('profile', 'preference') ?>"; }
        if(t=='blog')    { url = "<?php B_Helper::url('blog'   , 'preference') ?>"; }

        $.ajax
        ({
            type: "POST",
            url: url,
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
                k = d.find('k').text();
                v = d.find('v').text();

                if(t=='profile')
                {
                    if(k=='current_blog')    { set_blog(v); }
                    if(k=='feed_display')    { set_feed_display(v);    }
                    if(k=='article_display') { set_article_display(v); }
                }
                if(t=='blog')
                {
                    if(k=='queue_mode') { queue_set_mode(v); }
                }
            },
            error: function () { err(); }
        });
    }

    /* init queue */

    function toggle_queue_mode()
    {
        save_preference('blog', 'queue_mode', (queue_mode == 'manual' ? 'auto' : 'manual'));
    }

    function queue_set_mode(m)
    {
        queue_mode = m ? m : 'manual';

        if(queue_mode=='auto')
        {
            $("#queuemodeautolnk").hide();
            $("#queuemodeautolabel").show();
            $("#queuemodemanuallnk").show();
            $("#queuemodemanuallabel").hide();
        }
        if(queue_mode=='manual')
        {
            $("#queuemodeautolnk").show();
            $("#queuemodeautolabel").hide();
            $("#queuemodemanuallnk").hide();
            $("#queuemodemanuallabel").show();
        }
    }

    /* set blog */

    function set_blog(b)
    {
        if((current_blog = b))
        {
            load_preference('blog');
            set_feed_display(feed_display);
        }
    }

    /* TRIGGERS */

    $(window).resize(function()
    {
        window_update();
    });

    /* blog selector */

    blog_select_list.change(function()
    {
        save_preference('profile', 'current_blog', $(this).find("option:selected").val());
    });

    /* feed display */

    $("a#feeddsplnkall").click(function()
    {
        save_preference('profile', 'feed_display', 'all');
    });

    $("a#feeddsplnkthr").click(function()
    {
        save_preference('profile', 'feed_display', 'thr');
    });

    /* article display */

    $("a#articledsplnklst").click(function()
    {
        save_preference('profile', 'article_display', 'lst');
    });

    $("a#articledsplnkexp").click(function()
    {
        save_preference('profile', 'article_display', 'exp');
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

    $("a.queuehctrllnk").click(function()
    {
        queue_hctrl_display = (queue_hctrl_display < 2) ? queue_hctrl_display + 1 : 0;
        window_update();
    });

    $("#queuemode a").click(function()
    {
        toggle_queue_mode();
    });

    /* INIT */

    <?php if(count($this->blogs) > 0) : ?>
    load_preference('profile');
    <?php else : ?>
    $.b_dialog({ selector: "#noblogmsg", modal: false });
    $.b_dialog_show();
    <?php endif ?>
});
