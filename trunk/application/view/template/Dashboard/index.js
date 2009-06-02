var mytpl = null;

var feed = 
{
    display : "<?php echo $this->settings->feed->display ?>"
};

var article = 
{
    display : "<?php echo $this->settings->article->display ?>",
    data    : Array(),
    current : null
};

var queue = 
{
    height      : 0    , // 0 minimum | 1 half | 2 maximum
    publication : null ,
    interval    : null ,
    feeding     : null ,
    data        : Array()
};

var magic_q_min = 5;
var magic_q_max = 126;


function feed_area_enable()
{
    mytpl.feed_area_head.removeClass('areadisabled');
    mytpl.feed_dsp.show();
    mytpl.article_dsp.show();
    if(article.display=='list') // @see set_article_display()
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
    mytpl.queue_height_lnks.find('a').hide();
    mytpl.queue_height_med.show();
}

function queue_expanded(h)
{
    var _th = h - mytpl.queue_area.outerHeight();
    mytpl.queue_area.css('bottom', _th);
    mytpl.queue_list_area.css('height', _th);
    mytpl.queue_list_area.show();
    mytpl.queue_height_lnks.find('a').hide();
    mytpl.queue_height_max.show();
}

function queue_maximize()
{
    var _th = $(window).height() - magic_q_max;
    mytpl.queue_area.css('bottom', _th);
    mytpl.queue_list_area.css('height', _th);
    mytpl.feed_list_area.hide();
    feed_area_disable();
    mytpl.queue_height_lnks.find('a').hide();
    mytpl.queue_height_min.show();
}

function set_feed_display()
{
    article.data = Array();

    mytpl.feed_dsp_all.hide();
    mytpl.feed_dsp_threaded.hide();

    if(feed.display=='all')
    {
        mytpl.feed_dsp_all.show();
        article_list(mytpl.feed_list_area);
    }
    if(feed.display=='threaded') /* threaded */
    {
        mytpl.feed_dsp_threaded.show();
        feed_list();
    }

    mytpl.feed_list_area.scrollTop(0);
}

function set_article_display()
{
    mytpl.article_dsp_list.hide();
    mytpl.article_dsp_expanded.hide();

    if(article.display=='list') /* list */
    {
        mytpl.article_dsp_list.show();
        mytpl.feed_navigation.show();
        article_content_hide_all();
    }
    if(article.display=='expanded') /* expanded */
    {
        mytpl.article_dsp_expanded.show();
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
        mytpl.feed_list_area.html("<br/><span><?php echo $this->translation()->no_registered_feeds ?></span>");
    }

    /* this trigger must be created after populate, otherwise
     * will not work (because populate write elements after document loading */

    var _feed = null;

    mytpl.feed_list_area.find("div.feeditem").click(function()
    {
        _feed = $(this).attr('feed');

        if($(this).hasClass('feeditemthreaded'))
        {
            article_unthreadedead(_feed);
        }
        else
        {
            article_threadedead(_feed);
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
        data: { blog    : blog.current , 
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
    article.data = Array();

    if(feed.display=='all')
    {
        article_list(mytpl.feed_list_area);
    }
    if(feed.display=='threaded') /* threaded */
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
            feed               : $(this).find('feed').text(),
            feed_title         : $(this).find('feed_title').text(),
            article            : $(this).find('article').text(),
            article_title      : $(this).find('article_title').text(),
            article_link       : $(this).find('article_link').text(),
            article_date       : $(this).find('article_date').text(),
            article_author     : $(this).find('article_author').text(),
            article_content    : $(this).find('article_content').text(),
            publication_status : $(this).find('publication_status').text()
        };

        if(article.data[_data.article]==undefined) /* avoid duplicated items */
        {
            _item = mytpl.article_blank.clone();
            _inner = _item.find('div.article');
            _inner.attr('feed', _data.feed);
            _inner.attr('article', _data.article);
            _inner.addClass('article' + feed.display);

            if(feed.display == 'all')
            {
                _inner.find('div.articlefeed').show().text(_data.feed_title);
            }

            if(_data.publication_status.length>0)
            {
                _inner.find('div.articlequeue').find('input')
                    .replaceWith("<input type=\"checkbox\" checked/>"); /* FF does not check :P */
                queue_added_mark(_inner.find('div.articlequeue').find('input'));
            }

            _inner.find('div.articletitle')
                .text(_data.article_title.substring(0,80).replace(/\w+$/,'') + 
                ((_data.article_title.length>=80) ? '...' : ''));
            _inner.find('div.articleinfo').text("@" + _data.article_date);
            _inner.find('div.articlebuttons').find('a.viewlnk').attr('href', _data.article_link);

            _lsdata += _item.html() + "\n";
        }

        article.data[_data.article] =
        {
            title   : _data.article_title,
            author  : _data.article_author,
            content : _data.article_content
        };
    });

    container.append(_lsdata);
    container.append(mytpl.article_more_blank.clone()
        .find('div.articlemore').attr('older', _data.article_date));

    /* article triggers must be created after populate, otherwise
     * will not work (because populate write elements after document loading */

    var _feed  = null;
    var _label = null;

    container.find("div.article[bound='no']").each(function()
    {
        $(this).attr('bound', 'yes');

        $(this).find('div.articlequeue').find('input').click(function()
        {
            if($(this).attr('checked'))
            {
                queue_add($(this));
            }
        });

        _label = $(this).find('div.articlelabel');

        _label.hover
        (
            function() { $(this).parent().addClass('articlehover');    },
            function() { $(this).parent().removeClass('articlehover'); }
        );

        _label.click(function()
        {
            if(article.display == 'list')
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

    if(article.display == 'expanded')
    {
        article_content_show_all();
    }

    $(document).trigger('article_populated');
    $(document).trigger('article_populated_once'); // @see article_next()
    $(document).unbind('article_populated_once');
}

function __article_load(container, append, older)
{
    url = (feed.display == 'threaded') ? 
                "<?php B_Helper::url('article', 'threaded') ?>" : 
                "<?php B_Helper::url('article', 'all') ?>";
    $.ajax
    ({
        type: "GET",
        url: url,
        dataType: "xml",
        data: { blog  : blog.current , 
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

function article_threadedead(feed)
{
    var _b = mytpl.feed_list_area.find("div.feeditem[feed='" + feed + "']");;
    var _c = _b.next();
    article_list(_c);
    _c.show();
    _b.addClass('feeditemthreaded');
}

function article_unthreadedead(feed)
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

    if(article.current==null)
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
        _ca = article.current;
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
            _c = (feed.display=='threaded') ? _ca.parents() : mytpl.feed_list_area;
            if((_m = _c.find("div.articlemore")))
            {
                _m.click();
                $(document).bind('article_populated_once', function(e) { article_next(); });
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

    if((_ca = article.current))
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

    if((_b = article.current))
    {
        var _fa_p = mytpl.feed_list_area.position().top;
        var _fa_s = mytpl.feed_list_area.scrollTop();
        var _ah_p = _b.position().top;
        mytpl.feed_list_area.scrollTop(_fa_s + _ah_p - _fa_p);
    }
}

function article_content_show(a)
{
    if(article.data[a])
    {
        var _a = article.data[a];
        var _b = mytpl.feed_list_area.find("div.article[article='" + a + "']");
        var _c = _b.next(); // div.articlecontent
        var _d = "";

        if(_a.content)
        {
            if(_a.title)  { _d += "<h1>" + _a.title   + "</h1>";  }
            if(_a.author) { _d += "<h2>" + _a.author  + "</h2>";  }
                            _d += "<p>"  + _a.content + "</p>\n";
        }
        else
        {
            _d += "<?php echo $this->translation()->no_content ?>\n";
        }

        _c.html(_d);
        _b.addClass('articlecontentshow'); 
        _c.show();
        article.current = _b;
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

function article_content_hide(a)
{
    var b = mytpl.feed_list_area.find("div.article[article='" + a + "']"); 
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
        queue.publication = (blog.info['publication_auto']==1);
    }

    if(queue.publication)
    {
        mytpl.queue_publication_manual_label.hide();
        mytpl.queue_publication_manual_lnk.show();
        mytpl.queue_publication_automatic_lnk.hide();
        mytpl.queue_publication_automatic_label.show();
    }
    else
    {
        mytpl.queue_publication_manual_lnk.hide();
        mytpl.queue_publication_manual_label.show();
        mytpl.queue_publication_automatic_label.hide();
        mytpl.queue_publication_automatic_lnk.show();
    }
}

function set_queue_interval()
{
    // TODO
}

function set_queue_feeding()
{
    // TODO
}

function queue_populate(e)
{
    if(e.length==0)
    {
        // mytpl.queue_list_area.html("<br/><span><?php echo $this->translation()->no_entries ?></span>");
        return null;
    }

    var _data   = null;
    var _item   = null;
    var _inner  = null;
    var _lsdata = "";

    e.each(function()
    {
        _data =
        {
            entry              : $(this).find('entry').text(),
            entry_title        : $(this).find('entry_title').text(),
            entry_content      : $(this).find('entry_content').text(),
            publication_status : $(this).find('publication_status').text(),
            publication_date   : $(this).find('publication_date').text()
        };

        _item = mytpl.entry_blank.clone();
        _inner = _item.find('div.entry');
        _inner.attr('entry', _data.entry);

        if(_data.publication_status=='waiting')
        {
            _inner.find('div.entrypublish').find('input')
                .replaceWith("<input type=\"checkbox\" checked/>"); /* FF does not check :P */
            queue_waiting_mark(_inner.find('div.entrypublish').find('input'));
        }

        _inner.find('div.entrytitle')
            .text(_data.entry_title.substring(0,80).replace(/\w+$/,'') + 
            ((_data.entry_title.length>=80) ? '...' : ''));
        _inner.find('div.entryinfo').text("@" + _data.publication_date);

        _lsdata += _item.html() + "\n";

        queue.data[_data.entry] =
        {
            title   : _data.entry_title,
            content : _data.entry_content
        };
    });

    mytpl.queue_list_area.prepend(_lsdata);

    var _entry = null;
    var _label = null;

    mytpl.queue_list_area.find("div.entry[bound='no']").each(function()
    {
        $(this).attr('bound', 'yes');

        $(this).find('div.entrypublish').find('input').click(function()
        {
            if($(this).attr('checked'))
            {
                queue_publish($(this));
            }
        });

        _label = $(this).find('div.entrylabel');

        _label.hover
        (
            function() { $(this).parent().addClass('entryhover');    },
            function() { $(this).parent().removeClass('entryhover'); }
        );

        _label.click(function()
        {
            _entry = $(this).parent().attr('entry');

            if($(this).parent().hasClass('entrycontentshow'))
            {
                // entry_content_hide(_entry); // TODO
            }
            else
            {
                /* TODO
                entry_content_hide_all();
                entry_content_show(_entry);
                entry_scroll_top();
                */
            }
        });
    });

    $(document).trigger('queue_populated');
}

function queue_list()
{
    $.ajax
    ({
        type: "GET",
        url: "<?php B_Helper::url('queue', 'list') ?>",
        dataType: "xml",
        data: { blog : blog.current },
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
            mytpl.queue_list_area.html("");
            queue_populate(_d.find('queue').children());
        },
        error: function () { server_error(); }
    });
}

function queue_added_mark(c)
{
    c.attr('disabled', true).blur();
}

function queue_add(c)
{
    var _i = c.parent().parent();

    $.ajax
    ({
        type: "POST",
        url: "<?php B_Helper::url('queue', 'add') ?>",
        dataType: "xml",
        data: { blog    : blog.current ,
                feed    : _i.attr('feed') , 
                article : _i.attr('article') },
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
            queue_added_mark(c);
            queue_populate(_d.find('result'));
        },
        error: function () { server_error(); }
    });
}

function queue_publish(c)
{
    var _i = c.parent().parent();

    $.ajax
    ({
        type: "POST",
        url: "<?php B_Helper::url('queue', 'publish') ?>",
        dataType: "xml",
        data: { blog  : blog.current ,
                entry : _i.attr('entry') },
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
            queue_waiting_mark(c);
        },
        error: function () { server_error(); }
    });
}

function queue_waiting_mark(c)
{
    c.attr('disabled', true).blur();
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
        feed_dsp_threaded                 : $("#feeddspthreaded"),
        feed_dsp_threaded_lnk             : $("#feeddspthreadedlnk"),
        feed_item_blank                   : $("#feeditemblank"),
        article_dsp                       : $("#articledisplay"),
        article_dsp_list                  : $("#articledsplist"),
        article_dsp_list_lnk              : $("#articledsplistlnk"),
        article_dsp_expanded              : $("#articledspexpanded"),
        article_dsp_expanded_lnk          : $("#articledspexpandedlnk"),
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
        queue_area_heightbar              : $("#queueareaheightbar"),
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
        queue_height_lnks                 : $("#queueheightlnks"),
        queue_height_min                  : $("#queueheightmin"),
        queue_height_med                  : $("#queueheightmed"),
        queue_height_max                  : $("#queueheightmax"),
        queue_list_area                   : $("#queuelistarea"),
        entry_blank                       : $("#entryblank")
    };

    function window_update()
    {
        var _th = mytpl.top_bar.outerHeight() + mytpl.feed_area_head.outerHeight(),
            _bh = 0;

        if(queue.height == 0)
        {
            _bh = mytpl.queue_area_head.outerHeight() + magic_q_min;
            queue_minimize();
        }
        if(queue.height == 1)
        {
            _bh = $(window).height() / 2;
            queue_expanded(_bh);
        }
        if(queue.height == 2)
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
        queue_list();
    }

    /*<?php if(count($this->blogs)==0) : ?>**/

    $.b_dialog({ selector: "#noblogmsg", modal: false });
    $.b_dialog_show();

    /*<?php endif ?>**/

    blog_load();

    $(document).bind('blog_changed' , function(e)
    {
        blog_load();
    });

    $(document).bind('blog_loaded' , function(e)
    {
        init();
    });

    /* triggers */

    $(window).resize(function()
    {
        window_update();
    });

    mytpl.queue_height_lnks.find('a').click(function()
    {
        queue.height = (queue.height < 2) ? queue.height + 1 : 0;
        window_update();
        return false;
    });

    mytpl.feed_dsp_all_lnk.click(function()
    {
        feed.display = 'all';
        save_setting('feed', 'display', feed.display);
        return false;
    });

    mytpl.feed_dsp_threaded_lnk.click(function()
    {
        feed.display = 'threaded';
        save_setting('feed', 'display', feed.display);
        return false;
    });

    $(document).bind('setting_feed_display_saved' , function(e)
    {
        set_feed_display();
    });

    mytpl.article_dsp_list_lnk.click(function()
    {
        article.display = 'list';
        save_setting('article', 'display', article.display);
        return false;
    });

    mytpl.article_dsp_expanded_lnk.click(function()
    {
        article.display = 'expanded';
        save_setting('article', 'display', article.display);
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

    $(document).bind('setting_article_display_saved' , function(e)
    {
        set_article_display();
    });

    mytpl.queue_publication.find('a').click(function()
    {
        queue.publication = ($(this).attr('id')==mytpl.queue_publication_automatic_lnk.attr('id'));
        blog_update('publication_auto', (queue.publication ? 1 : 0));
        return false;
    });

    $(document).bind('blog_publication_auto_updated' , function(e)
    {
        set_queue_publication();
    });

    $(document).bind('article_populated' , function(e)
    {
        window_update();
    });

    $(document).bind('queue_populated' , function(e)
    {
        window_update();
    });
});
