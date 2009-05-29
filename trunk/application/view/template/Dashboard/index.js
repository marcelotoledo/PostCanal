var mytpl = null;

var feeds = 
{
    display : "<?php echo $this->preference->feed_display ?>"
};

var articles = 
{
    display : "<?php echo $this->preference->article_display ?>",
    content : Array(),
    current : null
};

var queue = 
{
    hctrl_display : 0    , // 0 min | 1 exp | 2 max
    publication   : null ,
    interval      : null ,
    feeding       : null
};

var magic_q_min = 5;
var magic_q_exp = 16;
var magic_q_max = 140;


function feed_area_enable()
{
    mytpl.feed_area_head.removeClass('areadisabled');
    mytpl.feed_dsp.show();
    mytpl.article_dsp.show();
    if(articles.display=='lst') // @see set_article_display()
    {
        mytpl.feed_navigation.show();
    }
    mytpl.feed_refresh.show();
}

function feed_area_disable()
{
    mytpl.feed_area_head.addClass('areadisabled');
    mytpl.feed_dsp.hide();
    mytpl.article_dsp.hide();
    mytpl.feed_navigation.hide();
    mytpl.feed_refresh.hide();
}

function queue_minimize()
{
    mytpl.queue_area.css('bottom', 0);
    mytpl.queue_list_area.hide();
    feed_area_enable();
    mytpl.feed_list_area.show();
    mytpl.queue_hctrl_lnks.find("a").hide();
    mytpl.queue_hctrl_exp.show();
}

function queue_expand(h)
{
    mytpl.queue_area.css('bottom', h - mytpl.queue_area.outerHeight() - magic_q_exp);
    mytpl.queue_list_area.show();
    mytpl.queue_hctrl_lnks.find("a").hide();
    mytpl.queue_hctrl_max.show();
}

function queue_maximize()
{
    mytpl.queue_area.css('bottom', $(window).height() - magic_q_max);
    mytpl.feed_list_area.hide();
    feed_area_disable();
    mytpl.queue_hctrl_lnks.find("a").hide();
    mytpl.queue_hctrl_min.show();
}

function set_feed_display()
{
    articles.content = Array();

    mytpl.feed_dsp_all.hide();
    mytpl.feed_dsp_thr.hide();

    if(feeds.display=='all')
    {
        mytpl.feed_dsp_all.show();
        article_list(mytpl.feed_list_area);
    }
    if(feeds.display=='thr') /* threaded */
    {
        mytpl.feed_dsp_thr.show();
        feed_list();
    }

    mytpl.feed_list_area.scrollTop(0);
}

function set_article_display()
{
    mytpl.article_dsp_lst.hide();
    mytpl.article_dsp_exp.hide();

    if(articles.display=='lst') /* list */
    {
        mytpl.article_dsp_lst.show();
        mytpl.feed_navigation.show();
        article_content_hide_all();
    }
    if(articles.display=='exp') /* expanded */
    {
        mytpl.article_dsp_exp.show();
        mytpl.feed_navigation.hide();
        article_content_show_all();
    }
}

function feed_populate(feeds)
{
    if(feeds.length > 0)
    {
        mytpl.feed_list_area.html("");

        var _data   = null;
        var _item   = null;
        var _inner  = null;
        var _lsdata = "";

        feeds.each(function()
        {
            _data =
            {
                feed  : $(this).find('feed').text(),
                title : $(this).find('feed_title').text()
            };

            _item = mytpl.feed_item_blank.clone();
            _inner = _item.find('div.feeditem');
            _inner.attr('feed', _data.feed);
            _inner.text(_data.title);

            _lsdata += _item.html() + "\n";
        });

        mytpl.feed_list_area.html(_lsdata);
    }
    else
    {
        feed_list_area.html("<br/><span><?php echo $this->translation()->no_registered_feeds ?></span>");
    }

    /* this trigger must be created after populate, otherwise
     * will not work (because populate write elements after document loading */

    var _feed = null;

    mytpl.feed_list_area.find("div.feeditem").click(function()
    {
        _feed = $(this).attr('feed');

        if($(this).hasClass('feeditemthreaded'))
        {
            article_unthread(_feed);
        }
        else
        {
            article_thread(_feed);
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
        data: { blog    : current_blog, 
                enabled : true },
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
            var _d = $(xml).find('data');
            feed_populate(_d.find('feeds').children());
        },
        error: function () { server_error(); }
    });
}

function feed_refresh()
{
    articles.content = Array();

    if(feeds.display=='all')
    {
        article_list(mytpl.feed_list_area);
    }
    if(feeds.display=='thr') /* threaded */
    {
        feed_list();
    }

    mytpl.feed_list_area.scrollTop(0);
}

function article_populate(a, container, append)
{
    if(a.length==0)
    {
        if(append==true)
        {
            container.find("div.articlemore").remove();
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
        container.find("div.articlemore").remove();
    }

    var _data   = null;
    var _item   = null;
    var _inner  = null;
    var _lsdata = "";

    a.each(function()
    {
        _data =
        {
            feed    : "",
            article : $(this).find('article').text(),
            title   : $(this).find('title').text(),
            link    : $(this).find('link').text(),
            date    : $(this).find('date').text(),
            author  : $(this).find('author').text(),
            content : $(this).find('content').text()
        };

        if(articles.content[_data.article]==undefined) /* avoid duplicated items */
        {
            _item = mytpl.article_blank.clone();
            _inner = _item.find('div.article');
            _inner.attr('article', _data.article);
            _inner.addClass('article' + feeds.display);

            if(feeds.display == 'all')
            {
                _data.feed = $(this).find('feed').text();
                _inner.find('div.articlefeed').show().text(_data.feed);
            }

            _inner.find('div.articletitle')
                .text(_data.title.substring(0,80).replace(/\w+$/,'') + 
                ((_data.title.length>=80) ? '...' : ''));
            _inner.find('div.articleinfo').text("@" + _data.date);
            _inner.find('div.articlebuttons').find('a.viewlnk').attr('href', _data.link);
            _inner.find('div.articlecontent').attr('article', _data.article);

            _lsdata += _item.html() + "\n";
        }

        articles.content[_data.article] =
        {
            title   : _data.title,
            author  : _data.author,
            content : _data.content
        };
    });

    container.append(_lsdata);
    container.append(mytpl.article_more_blank.clone()
        .find('div.articlemore').attr('older', _data.date));

    /* article triggers must be created after populate, otherwise
     * will not work (because populate write elements after document loading */

    var _feed = null;

    container.find("div.article[bound='no']").each(function()
    {
        $(this).attr('bound', 'yes');

        $(this).find('div.articlelabel').click(function(e)
        {
            if(articles.display == 'lst')
            {
                _feed = $(this).parent().attr('article');

                if($(this).parent().hasClass('articlecontentshow'))
                {
                    article_content_hide(_feed);
                }
                else
                {
                    article_content_hide_all();
                    article_content_show(_feed);
                    article_scroll_top();
                }
            }
        });
    });

    container.find("div.articlemore").click(function()
    {
        article_more(container, $(this).attr('older'));
    });

    if(articles.display == 'exp')
    {
        article_content_show_all();
    }

    $(document).trigger('article_populated');
    $(document).trigger('article_next_older_event'); // @see article_next()
    $(document).unbind('article_next_older_event');
}

function __article_load(container, append, older)
{
    url = (feeds.display == 'thr') ? 
                "<?php B_Helper::url('article', 'threaded') ?>" : 
                "<?php B_Helper::url('article', 'all') ?>";
    $.ajax
    ({
        type: "GET",
        url: url,
        dataType: "xml",
        data: { blog  : current_blog, 
                feed  : container.prev().attr('feed'), 
                older : older },
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
            var _d = $(xml).find('data');
            article_populate(_d.find('articles').children(), container, append);
        },
        error: function () { server_error(); }
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

function article_thread(feed)
{
    var _b = mytpl.feed_list_area.find("div.feeditem[feed='" + feed + "']");;
    var _c = _b.next();
    article_list(_c);
    _c.show();
    _b.addClass('feeditemthreaded');
}

function article_unthread(feed)
{
    var _b = mytpl.feed_list_area.find("div.feeditem[feed='" + feed + "']");;
    var _c = _b.next();
    _c.html("");
    _c.hide();
    _b.removeClass('feeditemthreaded');
}

function article_next()
{
    var _c  = null;
    var _j  = null;
    var _k  = null;
    var _m  = null;
    var _ca = null;
    var _na = null;

    if(articles.current==null)
    {
        _ca = mytpl.feed_list_area.find("div.article");
        _k = null;

        if(_k = _ca.attr('article'))
        {
            article_content_show(_k);
            article_scroll_top();
        }
    }
    else
    {
        _ca = articles.current;
        _na = _ca.nextAll("div.article");

           _j = _ca.attr('article');
        if(_k = _na.attr('article'))
        {
            article_content_hide(_j)
            article_content_show(_k);
            article_scroll_top();
        }
        else // try to load older articles
        {
            _c = (feeds.display=='thr') ? _ca.parents() : mytpl.feed_list_area;
            if((_m = _c.find("div.articlemore")))
            {
                _m.click();
                $(document).bind('article_next_older_event', function(e) { article_next(); });
            }
        }
    }
}

function article_previous()
{
    var _j  = null;
    var _k  = null;
    var _ca = null;
    var _pa = null;

    if((_ca = articles.current))
    {
        _pa = _ca.prevAll("div.article");

           _j = _ca.attr('article');
        if(_k = _pa.attr('article'))
        {
            article_content_hide(_j)
            article_content_show(_k);
            article_scroll_top();
        }
    }
}

function article_scroll_top()
{
    var _b = null;

    if((_b = articles.current))
    {
        var _fa_p = mytpl.feed_list_area.position().top;
        var _fa_s = mytpl.feed_list_area.scrollTop();
        var _ah_p = _b.position().top;
        mytpl.feed_list_area.scrollTop(_fa_s + _ah_p - _fa_p);
    }
}

function article_content_show(article)
{
    if(articles.content[article])
    {
        var a = articles.content[article];
        var b = mytpl.feed_list_area.find("div.article[article='" + article + "']");
        var c = b.next(); // div.articlecontent
        var d = "";

        if(a.content)
        {
            if(a.title)  { d += "<h1>" + a.title   + "</h1>";  }
            if(a.author) { d += "<h2>" + a.author  + "</h2>";  }
                           d += "<p>"  + a.content + "</p>\n";
        }
        else
        {
            d += "<?php echo $this->translation()->no_content ?>\n";
        }

        c.html(d);
        b.addClass('articlecontentshow'); 
        c.show();
        articles.current = b;
    }
}

function article_content_show_all()
{
    mytpl.feed_list_area.find("div.article").each(function()
    {
        if($(this).hasClass('articlecontentshow') == false)
        {
            article_content_show($(this).attr('article'));
        }
        $(this).hide(); // hide title
    });
}

function article_content_hide(article)
{
    var b = mytpl.feed_list_area.find("div.article[article='" + article + "']"); 
    var c = b.next(); // div.articlecontent
    c.html("");
    c.hide();
    b.removeClass('articlecontentshow'); 
    b.show(); // when hide
}

function article_content_hide_all()
{
    mytpl.feed_list_area.find("div.articlecontentshow").each(function()
    {
        article_content_hide($(this).attr('article'));
    });
}

function set_queue_publication()
{
    if(queue.publication==null)
    {
        if((queue.publication = current_blog_info['queue.publication'])==undefined)
        {
            queue.publication = 'manual';
        }
    }

    if(queue.publication=='manual')
    {
        mytpl.queue_publication_manual_lnk.hide();
        mytpl.queue_publication_manual_label.show();
        mytpl.queue_publication_automatic_label.hide();
        mytpl.queue_publication_automatic_lnk.show();
    }
    if(queue.publication=='automatic')
    {
        mytpl.queue_publication_manual_label.hide();
        mytpl.queue_publication_manual_lnk.show();
        mytpl.queue_publication_automatic_lnk.hide();
        mytpl.queue_publication_automatic_label.show();
    }
}

function set_queue_interval()
{
}

function set_queue_feeding()
{
    if(queue.feeding==null)
    {
        if((queue.feeding = current_blog_info['queue_feeding'])==undefined)
        {
            queue.feeding= 'manual';
        }
    }

    if(queue.feeding=='manual')
    {
        mytpl.queue_feeding_manual_lnk.hide();
        mytpl.queue_feeding_manual_label.show();
        mytpl.queue_feeding_automatic_label.hide();
        mytpl.queue_feeding_automatic_lnk.show();
    }
    if(queue.feeding=='automatic')
    {
        mytpl.queue_feeding_manual_label.hide();
        mytpl.queue_feeding_manual_lnk.show();
        mytpl.queue_feeding_automatic_lnk.hide();
        mytpl.queue_feeding_automatic_label.show();
    }
}


$(document).ready(function()
{
    mytpl =
    {
        top_bar                           : $("#topbar"),
        feed_area                         : $("#feedarea"),
        feed_area_head                    : $("#feedareahead"),
        feed_dsp                          : $("#feeddisplay"),
        feed_dsp_all                      : $("#feeddspall"),
        feed_dsp_all_lnk                  : $("#feeddspalllnk"),
        feed_dsp_thr                      : $("#feeddspthr"),
        feed_dsp_thr_lnk                  : $("#feeddspthrlnk"),
        feed_item_blank                   : $("#feeditemblank"),
        article_dsp                       : $("#articledisplay"),
        article_dsp_lst                   : $("#articledsplst"),
        article_dsp_lst_lnk               : $("#articledsplstlnk"),
        article_dsp_exp                   : $("#articledspexp"),
        article_dsp_exp_lnk               : $("#articledspexplnk"),
        article_blank                     : $("#articleblank"),
        article_more_blank                : $("#articlemoreblank"),
        feed_navigation                   : $("#feednavigation"),
        article_previous_lnk              : $("#articlepreviouslnk"),
        article_next_lnk                  : $("#articlenextlnk"),
        feed_refresh                      : $("#feedrefresh"),
        feed_refresh_lnk                  : $("#feedrefreshlnk"),
        feed_list_area                    : $("#feedlistarea"),
        queue_area                        : $("#queuearea"),
        queue_area_head                   : $("#queueareahead"),
        queue_area_hctrlbar               : $("#queueareahctrlbar"),
        queue_publication                 : $("#queuepublication"),
        queue_publication_manual_lnk      : $("#queuepublicationmanuallnk"),
        queue_publication_manual_label    : $("#queuepublicationmanuallabel"),
        queue_publication_automatic_lnk   : $("#queuepublicationautomaticlnk"),
        queue_publication_automatic_label : $("#queuepublicationautomaticlabel"),
        queue_interval                    : $("#queueinterval"),
        queue_interval_sel                : $("#queueintervalsel"),
        queue_feeding                     : $("#queuefeeding"),
        queue_feeding_manual_lnk          : $("#queuefeedingmanuallnk"),
        queue_feeding_manual_label        : $("#queuefeedingmanuallabel"),
        queue_feeding_automatic_lnk       : $("#queuefeedingautomaticlnk"),
        queue_feeding_automatic_label     : $("#queuefeedingautomaticlabel"),
        queue_hctrl_lnks                  : $("#queuehctrllnks"),
        queue_hctrl_min                   : $("#queuehctrlmin"),
        queue_hctrl_exp                   : $("#queuehctrlexp"),
        queue_hctrl_max                   : $("#queuehctrlmax"),
        queue_list_area                   : $("#queuelistarea")
    };

    function window_update()
    {
        var _th = mytpl.top_bar.outerHeight() + mytpl.feed_area_head.outerHeight();
            _bh = 0;

        if(queue.hctrl_display == 0)
        {
            _bh = mytpl.queue_area_head.outerHeight() + magic_q_min;
            queue_minimize();
        }
        if(queue.hctrl_display == 1)
        {
            _bh = $(window).height() / 2;
            queue_expand(_bh);
        }
        if(queue.hctrl_display == 2)
        {
            _bh = $(window).height();
            queue_maximize();
        }

        mytpl.feed_list_area.css('top', _th);
        mytpl.feed_list_area.css('left', 0);
        mytpl.feed_list_area.width($(window).width());
        mytpl.feed_list_area.height($(window).height() - _th - _bh);
        mytpl.feed_list_area.find('div.articlelabel').width($(window).width() * 0.6);
    }

    function init()
    {
        set_feed_display();
        set_article_display();
        set_queue_publication();
        mytpl.queue_publication.show();
        // set_queue_feeding();
        // mytpl.queue_feeding.show();
    }

    /*<?php if(count($this->blogs)==0) : ?>**/

    $.b_dialog({ selector: "#noblogmsg", modal: false });
    $.b_dialog_show();

    /*<?php endif ?>**/

    init();

    /* triggers */

    $(document).bind('blog_changed' , function(e)
    {
        init();
    });

    $(window).resize(function()
    {
        window_update();
    });

    mytpl.queue_hctrl_lnks.find("a").click(function()
    {
        queue.hctrl_display = (queue.hctrl_display < 2) ? queue.hctrl_display + 1 : 0;
        window_update();
    });

    mytpl.feed_dsp_all_lnk.click(function()
    {
        feeds.display = 'all';
        save_preference('feed_display', feeds.display);
        return false;
    });

    mytpl.feed_dsp_thr_lnk.click(function()
    {
        feeds.display = 'thr';
        save_preference('feed_display', feeds.display);
        return false;
    });

    $(document).bind('feed_display_saved' , function(e)
    {
        set_feed_display();
    });

    mytpl.article_dsp_lst_lnk.click(function()
    {
        articles.display = 'lst';
        save_preference('article_display', articles.display);
        return false;
    });

    mytpl.article_dsp_exp_lnk.click(function()
    {
        articles.display = 'exp';
        save_preference('article_display', articles.display);
        return false;
    });

    mytpl.feed_refresh_lnk.click(function()
    {
        feed_refresh();
        return false;
    });

    mytpl.article_previous_lnk.click(function()
    {
        article_previous();
        return false;
    });

    mytpl.article_next_lnk.click(function()
    {
        article_next();
        return false;
    });

    $(document).bind('article_display_saved' , function(e)
    {
        set_article_display();
    });

    $(document).bind('article_populated', function(e) 
    {
        window_update();
    });

    mytpl.queue_publication.find('a').click(function()
    {
        queue.publication = ($(this).attr('id')==mytpl.queue_publication_automatic_lnk.attr('id'))
            ? 'automatic' : 'manual' ;
        set_queue_publication();
        return false;
    });
});
