var mytpl = null;

var feed =
{
    current : null
};

var article = 
{
    display : "<?php echo $this->settings->article->display ?>",
    data    : Array(),
    current : null,
    older   : 0,
    bottom  : 0
};

var magic_slh = 10;


function set_article_display()
{
    if(article.display=='list')
    {
        mytpl.article_expanded_lab.hide();
        mytpl.article_expanded_lnk.show();
        mytpl.article_list_lnk.hide();
        mytpl.article_list_lab.show();
        article_hide_all();
        mytpl.article_prev.attr('disabled', false);
        mytpl.article_next.attr('disabled', false);
    }
    if(article.display=='expanded')
    {
        mytpl.article_expanded_lnk.hide();
        mytpl.article_expanded_lab.show();
        mytpl.article_list_lab.hide();
        mytpl.article_list_lnk.show();
        article_show_all();
        mytpl.article_prev.attr('disabled', true);
        mytpl.article_next.attr('disabled', true);
    }
}

/* FEEDS */

function feed_populate(d)
{
    mytpl.subscribed_list.html('');
    if(d.length==0) { return false; }

    var _data   = null;
    var _item   = null;
    var _inner  = null;
    var _lsdata = Array();
    var _i      = 0;

    d.each(function()
    {
        _data =
        {
            feed  : $(this).find('feed').text(),
            title : $(this).find('feed_title').text()
        };

        _item = mytpl.feed_item_blank.clone();
        _inner = _item.find('div.feeditem');
        _inner.attr('feed', _data.feed);
        _inner.find('a.feeditemlnk').text(_data.title);

        _lsdata[_i] = _item.html(); _i++;
    });

    mytpl.subscribed_list.html(_lsdata.join("\n"));
}

function feed_list_callback(d)
{
    feed_populate(d.find('feeds').children());
}

function feed_list()
{
    feed.current = null;

    var _data = { blog    : blog.current ,
                  enabled : true };

    do_request('GET', './feed/list', _data, feed_list_callback);
}

/* ARTICLES */

function update_right_header_title()
{
    var _title = "";

    if(feed.current)
    {
        _title = mytpl.subscribed_list
            .find("div.feeditem[feed='" + feed.current + "']")
            .find('a').text();
        mytpl.right_header_title.css('text-transform', 'none');
    }
    else
    {
        _title = "<?php echo $this->translation()->all_items ?>";
        mytpl.right_header_title.css('text-transform', 'capitalize');
    }

    mytpl.right_header_title.text(_title);
}

function article_populate(d, append)
{
    update_right_header_title();

    if(append==false)
    {
        mytpl.article_list.html('');
        article.data = Array();
        article.current = null;
        article.older = 0;
        article.bottom = 0;
    }

    if(d.length==0)
    { 
        if(append==true) { article.older = 0; }
        return false; 
    }

    var _data   = null;
    var _item   = null;
    var _inner  = null;
    var _lsdata = Array();
    var _i      = 0;

    d.each(function()
    {
        _data = 
        {
            feed               : $(this).find('feed').text(),
            feed_title         : $(this).find('feed_title').text(),
            article            : $(this).find('article').text(),
            article_title      : $(this).find('article_title').text(),
            article_link       : $(this).find('article_link').text(),
            article_date       : $(this).find('article_date').text(),
            article_time       : $(this).find('article_time').text(),
            article_date_local : $(this).find('article_date_local').text(),
            article_author     : $(this).find('article_author').text(),
            article_content    : $(this).find('article_content').text(),
            publication_status : $(this).find('publication_status').text(),
            entry              : $(this).find('entry').text()
        };

        if(article.data[_data.article]==undefined) // avoid dupl
        {
            _item  = mytpl.article_blank.clone();
            _inner = _item.find('div.article');
            _inner.attr('feed', _data.feed);
            _inner.attr('article', _data.article);
            _inner.attr('entry', _data.entry);

            if(_data.publication_status.length>0)
            {
                _inner.find('div.articlebutton')
                    .html('<input type="checkbox" checked/>');
            }

            _inner.find('div.articlesource').text(_data.feed_title);
            _inner.find('div.articletitle > a').text(_data.article_title);
            _inner.find('div.articledate').text(_data.article_date_local);
            _inner.find('a.articleview').attr('href', _data.article_link);

            _lsdata[_i] = _item.html(); _i++;
        }

        article.data[_data.article] =
        {
            title   : _data.article_title,
            author  : _data.article_author,
            content : _data.article_content
        };
    });

    mytpl.article_list.append(_lsdata.join("\n"));
    article.older = _data.article_time;

    if(article.display=='expanded') { article_show_all(); }
    if(append==false) { mytpl.article_list.scrollTop(0); }

    article.bottom += mytpl.article_list.find('div.article:last').position().top;
}

function article_list_callback(d)
{
    article_populate(d.find('articles').children(), (d.find('append').text()=="true"));
}

function article_list(older)
{
    var _url = ((feed.current) ? './article/threaded' : './article/all');

    var _data = { blog  : blog.current ,
                  feed  : feed.current ,
                  older : older };

    do_request('GET', _url, _data, article_list_callback);
}

function article_scroll_top()
{
    if(article.current)
    {
        mytpl.article_list.scrollTop(
            article.current.position().top -
            mytpl.article_list.position().top +
            mytpl.article_list.scrollTop() - 2
        );
    }
}

function article_show_fix_vertical()
{
    var _rmh = mytpl.right_middle_area.h / 2;
    var _rmt = mytpl.right_middle_area.y;
    var _apt = article.current.position().top;
    var _coh = article.current.next('div.content').outerHeight();

    var _scr = mytpl.article_list.scrollTop() + 
               _apt -
               ((_coh > _rmh) ? _rmt : _rmh);

    mytpl.article_list.scrollTop(_scr);
}

function article_hide(a)
{
    a.removeClass('articleopen').next('div.content').remove();
}

function article_hide_current()
{
    if(article.current) { article_hide(article.current); }
    article.current = null;
}

function article_hide_all()
{
    mytpl.article_list.find('div.content').remove();
    mytpl.article_list.find('div.article').removeClass('articleopen');
    article.current = null;
}

function article_show(a)
{
    if(article.data[a])
    {
        var _content = mytpl.content_blank.clone();

        _content.find('div.contentauthor').html(article.data[a].author);
        _content.find('div.contenttitle').html(article.data[a].title);
        _content.find('div.contentbody').html(article.data[a].content);

        article.current = mytpl.article_list.find("div.article[article='" + a + "']");
        article.current.after(_content.html()).addClass('articleopen');
    }
}

function article_show_all()
{
    article_hide_all();

    mytpl.article_list.find('div.article').each(function()
    {
        article_show($(this).attr('article'));
    });
}

function article_previous()
{
    var _prev = null;

    if(article.current)
    {
        _prev = article.current.prevAll('div.article').attr('article');
    }

    if(_prev) 
    { 
        article_hide_current(); 
        article_show(_prev); 
        article_scroll_top();
    }
}

function article_next()
{
    var _next = null;

    if(article.current)
    {
        _next = article.current.nextAll('div.article').attr('article');
    }
    else
    {
        _next = mytpl.article_list.find('div.article').attr('article');
    }

    if(_next)
    {
        article_hide_current();
        article_show(_next);
        article_scroll_top();
    }
}

/* QUEUE */

function queue_add_callback(d)
{
    var _sel = "div.article" +
               "[feed='" + d.find('feed').text() + "']" + 
               "[article='" + d.find('article').text() + "']";

    mytpl.article_list
        .find(_sel)
        .attr('entry', d.find('entry').text())
        .find('div.articlebutton')
        .find('input')
        .attr('checked', true)
        .blur();
}

function queue_add(f, a)
{
    var _data = { blog    : blog.current ,
                  feed    : f,
                  article : a };

    do_request('GET', './queue/add', _data, queue_add_callback);
}

function queue_delete_callback(d)
{
    var _sel = "div.article[entry='" + d.find('entry').text() + "']";

    mytpl.article_list.find(_sel)
        .find('div.articlebutton')
        .find('input')
        .attr('checked', false);
}

function queue_delete(e)
{
    var _data = { blog  : blog.current , entry : e };
    do_request('POST', './queue/delete', _data, queue_delete_callback);
}

function on_blog_change()
{
    feed_list();
    article_list(null);
}

$(document).ready(function()
{
    mytpl =
    {
        main_container       : $("#maincontainer"),
        left_container       : $("#leftcontainer"),
        all_items            : $("#allitems"),
        right_container      : $("#rightcontainer"),
        right_header_title   : $("#rightheadertitle"),
        right_middle         : $("#rightmiddle"),
        article_list         : $("#rightmiddle"),
        article_blank        : $("#articleblank"),
        content_blank        : $("#contentblank"),
        right_middle_area    : { x : 0 , y : 0 , w : 0 , h : 0 },
        right_middle_hover   : false,
        right_footer         : $("#rightfooter"),
        subscribed_list      : $("#subscribedfeedslist"),
        feed_item_blank      : $("#feeditemblank"),
        article_expanded_lnk : $("#articleexpandedlnk"),
        article_expanded_lab : $("#articleexpandedlab"),
        article_list_lnk     : $("#articlelistlnk"),
        article_list_lab     : $("#articlelistlab"),
        article_next         : $("#articlenext"),
        article_prev         : $("#articleprev")
    }; 
    
    function window_update()
    {
        var _w = { height : $(window).height() - 
                            mytpl.main_container.position().top,
                   width  : $(window).width() };

        mytpl.left_container.height(_w.height);
        mytpl.right_container.width(_w.width -
                                    mytpl.left_container.width());
        mytpl.right_container.height(_w.height);
        mytpl.right_container.css('left', mytpl.left_container.width());
        mytpl.subscribed_list.height(_w.height - 
                                     mytpl.subscribed_list.position().top - 
                                     magic_slh);
        mytpl.right_middle.height(_w.height - mytpl.right_middle.offset().top);

        mytpl.right_middle_area.x = mytpl.right_middle.offset().left;
        mytpl.right_middle_area.y = mytpl.right_middle.offset().top;
        mytpl.right_middle_area.w = mytpl.right_middle.width();
        mytpl.right_middle_area.h = mytpl.right_middle.height();
    }

    function initialize()
    {
        set_article_display();
        feed_list();
        article_list(null);
        window_update();
    }

    /* events */

    mytpl.article_list_lnk.click(function()
    {
        if(active_request==false)
        {
            article.display = 'list';
            save_setting('article', 'display', article.display);
        }
        return false;
    });

    mytpl.article_expanded_lnk.click(function()
    {
        if(active_request==false)
        {
            article.display = 'expanded';
            save_setting('article', 'display', article.display);
        }
        return false;
    });

    $(document).bind('setting_article_display_saved' , function(e)
    {
        set_article_display();
    });


    $(window).resize(function()
    {
        window_update();
    });

    function on_mouse_wheel(e)
    {
        if(mytpl.right_middle_hover==true && $.browser.msie) // Emulate wheel scroll on IE
        {
            var j = null;
            e = e ? e : window.event;
            j = e.detail ? e.detail * -1 : e.wheelDelta / 2;
            mytpl.right_middle.scrollTop(mytpl.right_middle.scrollTop() - j);
            return false;
        }
    }

    $(window).bind('DOMMouseScroll', function(e)
    {
        on_mouse_wheel(e);
    });

    $(document).bind('onmousewheel', function(e) /* Mozilla */
    {
        on_mouse_wheel(e);
    });

    window.onmousewheel = document.onmousewheel = on_mouse_wheel; /* IE */

    mytpl.right_middle.scroll(function()
    {
        // if(mytpl.article_list.scrollTop() > (article.bottom * 2/3) && /* fails when threaded */
        if(mytpl.article_list.scrollTop() > (article.bottom / 2) &&
           article.older > 0 && active_request==false)
        {
            article_list(article.older);
        }
    });

    function mouse_is_over_area(x, y, a)
    {
        return ((x >= a.x && x <= (a.x + a.w)) && (y >= a.y && y <= (a.y + a.h)));
    }

    function on_mouse_move(e)
    {
        var _mp = { x : 0 , y : 0 };
        
        e = e ? e : window.event;

        _mp.x = (e.pageX) ? e.pageX : e.clientX + document.body.scrollLeft;
        _mp.y = (e.pageY) ? e.pageY : e.clientY + document.body.scrollTop;

        mytpl.right_middle_hover = mouse_is_over_area(_mp.x, _mp.y, mytpl.right_middle_area);
    }

    $(window).bind('mousemove', function(e) /* Mozilla */
    {
        on_mouse_move(e);
    }); 

    window.onmousemove = document.onmousemove = on_mouse_move; /* IE */

    /* controls */

    mytpl.all_items.click(function()
    {
        feed.current = null;
        article_list(null);
        return false;
    });

    mytpl.subscribed_list.find('a.feeditemlnk').live('click', function()
    {
        feed.current = $(this).parent().attr('feed');
        article_list(null);
        $(this).blur();
        return false;
    });

    mytpl.article_list.find('div.article')
        .find('div.articlehead')
        .find('div.articletitle')
        .find('a').live('click', function()
    {
        var _pt = $(this).parent().parent().parent();

        if(_pt.hasClass('articleopen'))
        {
            article_hide_current();
            $(this).blur();
            return false; 
        }

        if(article.display=='list')
        {
            article_hide_current();
            article_show(_pt.attr('article'));
            article_show_fix_vertical();
        }

        $(this).blur();
        return false;
    });

    mytpl.article_list.find('div.article')
        .find('div.articlebutton')
        .find('input').live('change', function()
    {
        if($(this).attr('checked'))
        {
            var _i = $(this).parent().parent();
            queue_add(_i.attr('feed'), _i.attr('article'));
        }
        else
        {
            var _i = $(this).parent().parent();
            queue_delete(_i.attr('entry'));
        }

        $(this).blur();
        return false;
    });

    mytpl.article_next.click(function()
    {
        article_next();
    });

    mytpl.article_prev.click(function()
    {
        article_previous();
    });

    /* initialize */

    $(document).bind('blog_changed' , function(e)
    {
        on_blog_change();
    });

    initialize();
});
