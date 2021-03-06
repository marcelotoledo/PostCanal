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
    height         : parseInt("<?php echo $this->settings->queue->height ?>"), // 0 minimum | 1 half | 2 maximum
    publication    : null ,
    interval       :    0 ,
    enqueueing     : null ,
    data           : Array(),
    active_request : false,
    entry          : null,
    sorting        : false,
    editor         : null
};

var magic_q_min = 5;
var magic_q_max = 126;
var magic_fscrl = 50;
var updater_interval = 15000;


function checkbox_freeze(c)
{
    c.attr('disabled', true).blur();
}

function checkbox_unfreeze(c)
{
    c.attr('disabled', false).blur();
    c.attr('checked', false);
}

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
    mytpl.queue_list_area.show();
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
        mytpl.feed_list_area.html("");
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
        url: "/feed/list",
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
            container.html("");
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
    var _lsdata = Array();
    var _i      = 0;

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
            article_date_local : $(this).find('article_date_local').text(),
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
                if(_data.publication_status.length>0)
                {
                    _inner.find('div.articlequeue')
                        .html("<input type=\"checkbox\" checked/>");
                    checkbox_freeze(_inner.find('div.articlequeue').find('input'));
                }
            }

            _inner.find('div.articletitle').text(_data.article_title);
            _inner.find('div.articleinfo').text("@" + _data.article_date_local);
            _inner.find('div.articlebuttons').find('a.viewlnk').attr('href', _data.article_link);

            _lsdata[_i] = _item.html(); _i++;
        }

        article.data[_data.article] =
        {
            title   : _data.article_title,
            author  : _data.article_author,
            content : _data.article_content
        };
    });

    container.append(_lsdata.join("\n"));
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
                    article_fix_vertical_display();
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
                "/article/threaded" : 
                "/article/all";

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

function article_fix_vertical_display()
{
    var _ac_pt = article.current.position().top;
    var _fa_st = mytpl.feed_list_area.scrollTop();

    if(_ac_pt < magic_fscrl)
    {
        mytpl.feed_list_area.scrollTop(_fa_st + _ac_pt - magic_fscrl);
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
    queue.interval = blog.info['publication_interval'];
    mytpl.queue_interval_sel.find("option").each(function()
    {
        if($(this).val()==queue.interval)
        {
            $(this).attr('selected', true);
        }
    });
}

function set_queue_publication_auto()
{
    $.ajax
    ({
        type: "POST",
        url: "/queue/auto",
        dataType: "xml",
        data: { blog        : blog.current,
                interval    : queue.interval,
                publication : ((queue.publication==true) ? 1 : 0) },
        beforeSend: function()
        {
            set_active_request(true);
        },
        complete: function()
        {
            set_active_request(false);
            queue_list();
        },
        error: function () { server_error(); }
    });
}

function set_auto_enqueue()
{
    if(queue.enqueueing==null)
    {
        queue.enqueueing = (blog.info['enqueueing_auto']==1);
    }

    if(queue.enqueueing)
    {
        mytpl.auto_enqueue_manual_label.hide();
        mytpl.auto_enqueue_manual_lnk.show();
        mytpl.enqueueing_automatic_lnk.hide();
        mytpl.enqueueing_automatic_label.show();
    }
    else
    {
        mytpl.auto_enqueue_manual_lnk.hide();
        mytpl.auto_enqueue_manual_label.show();
        mytpl.enqueueing_automatic_label.hide();
        mytpl.enqueueing_automatic_lnk.show();
    }
}

function queue_entry_set_status(e, s)
{
    if(typeof e == 'string' && e.length > 0)
    {
        e = mytpl.queue_list_area.find("div.entry[entry='" + e + "']");
    }

    if(typeof e == 'object')
    {
        e.attr('status', s);

        if(s=='waiting')
        {
            e.find('div.entrypublish')
                .html("<input type=\"checkbox\" checked/>");
            checkbox_freeze(e.find('div.entrypublish').find('input'));
        }
        if(s=='failed')
        {
            e.find('div.entrypublish')
                .html("<nobr><input type=\"checkbox\"/><img src=\"/image/warning.png\"/></nobr>");
        }
        if(s=='published')
        {
            e.find('div.entrypublish').html("<b>[P]</b>");
        }
    }
}

function queue_set(e)
{
    mytpl.queue_list_area
        .find("div.entry[entry='" + e + "']")
        .find("div.entrylabel")
        .find("div.entrytitle").css('font-weight','bold');
}

function queue_unset(e)
{
    mytpl.queue_list_area
        .find("div.entry[entry='" + e + "']")
        .find("div.entrylabel")
        .find("div.entrytitle").css('font-weight','normal');
}

function queue_populate(e)
{
    if(e.length==0)
    {
        return null;
    }

    var _data   = null;
    var _item   = null;
    var _inner  = null;
    var _lsdata = Array();
    var _i      = 0;

    e.each(function()
    {
        _data =
        {
            entry                  : $(this).find('entry').text(),
            entry_title            : $(this).find('entry_title').text(),
            entry_content          : $(this).find('entry_content').text(),
            publication_status     : $(this).find('publication_status').text(),
            publication_date       : $(this).find('publication_date').text(),
            publication_date_local : $(this).find('publication_date_local').text(),
            ordering               : $(this).find('ordering').text()
        };

        _item = mytpl.entry_blank.clone();
        _inner = _item.find('div.entry');
        _inner.attr('entry', _data.entry);
        _inner.attr('ord', _data.ordering);

        queue_entry_set_status(_inner, _data.publication_status);

        _inner.find('div.entrytitle').text(_data.entry_title);

        if(_data.publication_status=='waiting' || 
           _data.publication_status=='published')
        {
            _inner.find('div.entryinfo').text("@" + _data.publication_date_local);
        }

        if(_data.publication_status!='published' &&
           queue.publication!=true)
        {
            _inner.find('div.entrybuttons').show();
        }
        
        /* disable all events for published */

        if(_data.publication_status=='published' ||
           queue.publication==true)
        {
            _inner.attr('bound','yes');
        }

        _lsdata[_i] = _item.html(); _i++;

        queue.data[_data.entry] =
        {
            title   : _data.entry_title,
            content : _data.entry_content
        };
    });

    var _publs = mytpl.queue_list_area.find("div.entry[status='published']").eq(0);

    if(_publs.length>0)
    {
        _publs.before(_lsdata.join("\n"));
    }
    else
    {
        mytpl.queue_list_area.append(_lsdata.join("\n"));
    }

    var _entry = null;
    var _label = null;
    var _buttn = null;

    mytpl.queue_list_area.find("div.entry[bound='no']").each(function()
    {
        $(this).attr('bound', 'yes');

        $(this).find('div.entrypublish').find('input').click(function()
        {
            if($(this).attr('checked'))
            {
                queue_publish($(this).closest('div.entry'));
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
                entry_content_hide(_entry);
            }
            else
            {
                entry_content_hide_all();
                entry_content_show(_entry);
                entry_fix_vertical_display();
            }
        });

        _buttn = $(this).find('div.entrybuttons');

        _buttn.find("a.queuedeletelnk").click(function()
        {
            _entry = $(this).parent().parent().attr('entry');

            queue_set(_entry);

            if(confirm("<?php echo $this->translation()->are_you_sure ?>"))
            {
                queue_delete(_entry);
            }
            else
            {
                queue_unset(_entry);
            }
            return false;
        });

        _buttn.find("a.queueeditlnk").click(function()
        {
            entry_content_hide_all();
            entry_content_edit($(this).parent().parent().attr('entry'));
            entry_scroll_top();
            $(this).blur();
            return false;
        });
    });

    $(document).trigger('queue_populated');
}

function entry_fix_vertical_display()
{
    var _qe_pt = queue.entry.position().top;
    var _qa_st = mytpl.queue_list_area.scrollTop();

    if(_qe_pt < 0)
    {
        mytpl.queue_list_area.scrollTop(_qa_st + _qe_pt);
    }
}

function entry_scroll_top()
{
    var _b = null;

    if((_b = queue.entry))
    {
        var _fa_s = mytpl.queue_list_area.scrollTop();
        var _ah_p = _b.position().top;
        mytpl.queue_list_area.scrollTop(_fa_s + _ah_p);
    }
}

function entry_content_show(e)
{
    if(queue.data[e] && queue.sorting==false)
    {
        var _a = queue.data[e];
        var _b = mytpl.queue_list_area.find("div.entry[entry='" + e + "']");
        var _c = "";

        if(_a.content)
        {
            if(_a.title)  { _c += "<h1>" + _a.title   + "</h1>";  }
                            _c += "<p>"  + _a.content + "</p>\n";
        }
        else
        {
            _c += "<?php echo $this->translation()->no_content ?>\n";
        }

        _b.after("<div class=\"entrycontent\">" + _c + "</div>");
        _b.addClass('entrycontentshow'); 
        queue.entry = _b;
    }
    queue.sorting=false;
}

function entry_content_hide(e)
{
    var b = mytpl.queue_list_area.find("div.entry[entry='" + e + "']"); 
    b.removeClass('entrycontentshow');
    b.next("div.entrycontent").remove();
}

function entry_content_hide_all()
{
    mytpl.queue_list_area.find("div.entry").removeClass('entrycontentshow');
    mytpl.queue_list_area.find("div.entrycontent").remove();
}

function FCKeditor_OnComplete(i)
{
    i.SetData(queue.data[queue.entry.attr('entry')].content);
}

function entry_content_edit(e)
{
    if(queue.data[e])
    {
        entry_content_hide_all();

        var _a = queue.data[e];
        var _b = mytpl.queue_list_area.find("div.entry[entry='" + e + "']");

        _b.after("<div class=\"entrycontent\">&nbsp;</div>");

        _c = _b.next("div.entrycontent");
        _c.html(mytpl.entry_edit_blank.clone().html());
        _c.find("input.entryedittitle").val(_a.title);
        _c.find("div.entryeditcontent").replaceWith(queue.editor.CreateHtml());

        _c.find("input.entryeditcancel").click(function()
        {
            entry_content_hide(queue.entry.attr('entry'));
        });

        _c.find("input.entryeditsubmit").click(function()
        {
            entry_content_update();
        });

        _b.addClass('entrycontentshow'); 
        queue.entry = _b;
    }
}

function entry_content_update_local(r)
{
    var _e = r.find('entry').text();
    queue.data[_e].title = r.find('title').text();
    queue.data[_e].content = r.find('content').text();
    queue.entry.find("div.entrylabel").find("div.entrytitle").html(queue.data[_e].title);
    entry_content_hide(_e);
}

function entry_content_update()
{
    var _a = queue.entry.next("div.entrycontent");
    var _fck = FCKeditorAPI.GetInstance("FCKQueueEntryEditor");

    $.ajax
    ({
        type: "POST",
        url: "/queue/update",
        dataType: "xml",
        data: { blog    : blog.current , 
                entry   : queue.entry.attr('entry'), 
                title   : _a.find("input.entryedittitle").val(),
                content : _fck.GetData() },
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
            var _r = $(xml).find('data').find('result');

            if(_r.length>0)
            {
                entry_content_update_local(_r);
            }
            else
            {
                server_error();
            }
        },
        error: function () { server_error(); }
    });
}

function queue_sortable_callback(entry)
{
    var _p = 1;

    mytpl.queue_list_area.find('div.entry').each(function()
    {
        if(entry == $(this).attr('entry') && _p != $(this).attr('ord'))
        {
            queue_position(entry, _p);
        }

        _p++;
    });
}

function queue_sortable_init()
{
    if(queue.publication==true)
    {
        mytpl.queue_list_area.sortable('destroy');
        return false;
    }

    mytpl.queue_list_area.sortable(
    { 
        handle: "div.entrytitle",
        items: "div.entry[status!='published']",
        cancel: "div.entrycontentshow",
        distance: 10,
        start: function(e,u)
        {
            queue.sorting = true;
        },
        update: function(e, u)
        {
            queue_sortable_callback(u.item.attr('entry'));
        }
    });
    mytpl.queue_list_area.disableSelection();
}

function queue_position(entry, position)
{
    $.ajax
    ({
        type: "POST",
        url: "/queue/position",
        dataType: "xml",
        data: { blog     : blog.current , 
                entry    : entry, 
                position : position },
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

            if((_d.find('updated').text()=="true")!=true)
            {
                queue_list();
            }
        },
        error: function () { server_error(); }
    });
}

function queue_list()
{
    $.ajax
    ({
        type: "GET",
        url: "/queue/list",
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
            queue_populate(_d.find('result').find('queue').children());
            queue_populate(_d.find('result').find('published').children());
        },
        error: function () { server_error(); }
    });
}

function queue_add(c)
{
    var _i = c.parent().parent();

    $.ajax
    ({
        type: "POST",
        url: "/queue/add",
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
            checkbox_freeze(c);
            queue_populate(_d.find('result'));
        },
        error: function () { server_error(); }
    });
}

function queue_remove_from_list(d)
{
    var _e = mytpl.queue_list_area.find("div.entry[entry='" + d.find('entry').text() + "']");
        _e.next("div.entrycontent").remove();
        _e.remove()
    checkbox_unfreeze(mytpl.feed_list_area.find("div.article[article='" + d.find('article').text() + "']").find('div.articlequeue').find('input'));
}

function queue_delete(e)
{
    $.ajax
    ({
        type: "POST",
        url: "/queue/delete",
        dataType: "xml",
        data: { blog  : blog.current ,
                entry : e },
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
            queue_remove_from_list($(xml).find('data'));
        },
        error: function () { server_error(); }
    });
}

function queue_publish(e)
{
    $.ajax
    ({
        type: "POST",
        url: "/queue/publish",
        dataType: "xml",
        data: { blog  : blog.current ,
                entry : e.attr('entry') },
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
            queue_entry_set_status($(xml).find('result').text(), 'waiting');
        },
        error: function () { server_error(); }
    });
}

function queue_entry_editor_init()
{
    queue.editor = new FCKeditor("FCKQueueEntryEditor");
    // _fck.Config["CustomConfigurationsPath"] = "/js/fckconfig.js?t=<?php echo time() ?>";
    queue.editor.Config["CustomConfigurationsPath"] = "/js/fckconfig.js";
    queue.editor.Config["AutoDetectLanguage"] = false ;
    queue.editor.Config["DefaultLanguage"] = "<?php echo substr($this->session()->getCulture(), 0, 2) ?>" ;
}

function queue_publication_updater_callback(r)
{
    r.each(function()
    {
        queue_entry_set_status($(this).find('entry').text(),
                               $(this).find('status').text());
    });
}

function queue_publication_updater()
{
    var _wdom = mytpl.queue_list_area.find("div.entry[status='waiting']");
    var _wpar = Array();

    if(_wdom.length > 0)
    {
        if(queue.active_request == false)
        {
            _wdom.each(function() { _wpar.push($(this).attr('entry')); });

            $.ajax
            ({
                type: "GET",
                url: "/queue/check",
                dataType: "xml",
                data: { blog    : blog.current ,
                        waiting : _wpar.join(',') },
                beforeSend: function()
                {
                    queue.active_request = true;
                },
                complete: function()
                {
                    queue.active_request = false;
                },
                success: function (xml)
                {
                    queue_publication_updater_callback($(xml).find('data')
                                                 .find('result')
                                                 .children());
                },
                error: function () { /* void */ }
            });
        }
    }
}

function auto_enqueue_updater()
{
    if(queue.enqueueing!=true) { return false; }
    if(queue.active_request==true) { return false; }

    $.ajax
    ({
        type: "GET",
        url: "/queue/list",
        dataType: "xml",
        data: { blog : blog.current },
        beforeSend: function()
        {
            queue.active_request = true;
        },
        complete: function()
        {
            queue.active_request = false;
        },
        success: function (xml)
        {
            $(xml).find('data').find('result').find('queue').children().each(function()
            {
                if(queue.data[($(this).find('entry').text())]==undefined)
                {
                    queue_populate($(this));
                }
            });
        },
        error: function () { /* void */ }
    });
}

function updater_run()
{
    var _i = updater_interval / 3;
    setTimeout("queue_publication_updater()", _i * 1);
    setTimeout("auto_enqueue_updater()", _i * 2);
    updater_init();
}

function updater_init()
{
    setTimeout("updater_run()", updater_interval);
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
        auto_enqueue                      : $("#enqueueing"),
        auto_enqueue_manual_lnk           : $("#enqueueingmanuallnk"),
        auto_enqueue_manual_label         : $("#enqueueingmanuallabel"),
        enqueueing_automatic_lnk          : $("#enqueueingautomaticlnk"),
        enqueueing_automatic_label        : $("#enqueueingautomaticlabel"),
        queue_height_lnks                 : $("#queueheightlnks"),
        queue_height_min                  : $("#queueheightmin"),
        queue_height_med                  : $("#queueheightmed"),
        queue_height_max                  : $("#queueheightmax"),
        queue_list_area                   : $("#queuelistarea"),
        entry_blank                       : $("#entryblank"),
        entry_edit_blank                  : $("#entryeditblank")
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
    }

    function init()
    {
        set_feed_display();
        set_article_display();

        set_queue_publication();
        mytpl.queue_publication.show();

        set_queue_interval();
        mytpl.queue_interval.show();

        set_auto_enqueue();
        mytpl.auto_enqueue.show();

        window_update();
        queue_list();

        updater_init();
        queue_entry_editor_init();
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

    $(document).bind('article_populated' , function(e)
    {
        window_update();
    });

    $(document).bind('queue_populated' , function(e)
    {
        window_update();
        queue_sortable_init();
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
        set_queue_publication_auto();
    });

    mytpl.queue_interval_sel.change(function()
    {
        if((queue.interval = $(this).find("option:selected").val()))
        {
            $(this).blur();
            blog_update('publication_interval', queue.interval);
        }
    });

    $(document).bind('blog_publication_interval_updated' , function(e)
    {
        set_queue_publication_auto();
    });

    mytpl.auto_enqueue.find('a').click(function()
    {
        queue.enqueueing = ($(this).attr('id')==mytpl.enqueueing_automatic_lnk.attr('id'));
        blog_update('enqueueing_auto', (queue.enqueueing ? 1 : 0));
        return false;
    });

    $(document).bind('blog_enqueueing_auto_updated' , function(e)
    {
        set_auto_enqueue();
    });

    mytpl.queue_height_lnks.find('a').click(function()
    {
        queue.height = (queue.height < 2) ? queue.height + 1 : 0;
        save_setting('queue', 'height', queue.height);
        return false;
    });

    $(document).bind('setting_queue_height_saved' , function(e)
    {
        window_update();
    });


    $(document).bind('setting_queue_height_saved' , function(e)
    {
        window_update();
    });

    $(document).bind('setting_queue_height_saved' , function(e)
    {
        window_update();
    });

});
