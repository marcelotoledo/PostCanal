var my_template = null;

var my_feed =
{
    current : null
};

var my_article = 
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
    if(my_article.display=='list')
    {
        my_template.article_expanded_lab.hide();
        my_template.article_expanded_lnk.show();
        my_template.article_list_lnk.hide();
        my_template.article_list_lab.show();
        article_hide_all();
        //my_template.article_prev.attr('disabled', false);
        //my_template.article_next.attr('disabled', false);
    }
    if(my_article.display=='expanded')
    {
        my_template.article_expanded_lnk.hide();
        my_template.article_expanded_lab.show();
        my_template.article_list_lab.hide();
        my_template.article_list_lnk.show();
        article_show_all();
        //my_template.article_prev.attr('disabled', true);
        //my_template.article_next.attr('disabled', true);
    }
}

/* FEEDS */

function feed_item_c(f, t, w)
{
    var _item = null;
    var _inner = null;

    _item = my_template.feed_item_blank.clone();
    _inner = _item.find('div.ch');
    _inner.attr('feed', f);
    _inner.find('a.feeditemlnk').attr('title', t).b_txtoverflow({ buffer: my_template.txtoverflow_buffer, width: w, text: t });

    return _item;
}

function feed_populate(d)
{
    if(d.length==0) { return false; }

    var _data   = null;
    var _item   = null;
    var _inner  = null;
    var _lsdata = Array();
    var _i      = 0;

    var _flw = my_template.subscribed_list.width() * 0.9;

    d.each(function()
    {
        _data =
        {
            feed  : $(this).find('feed').text(),
            title : $(this).find('feed_title').text()
        };

        _item = feed_item_c(_data.feed, _data.title, _flw);
        _lsdata[_i] = _item.html(); _i++;
    });

    my_template.subscribed_list.append(_lsdata.join("\n"));
}

function no_feed()
{
    //my_template.middle_menu.hide();
    //$("div.midct").hide();
    my_template.no_feed_message.show();
}

function feed_list_callback(d)
{
    var _fl = d.find('feeds').children();
    if(_fl.length==0) { no_feed(); } // tutorial
    feed_populate(_fl);
}

function feed_list()
{
    my_feed.current = null;

    var _data = { blog    : my_blog.current ,
                  enabled : true };

    do_request('GET', '/feed/list', _data, feed_list_callback);
}

function feed_add_callback(d)
{
    var _flw = my_template.subscribed_list.width() * 0.9;
    var _dta = d.find('feed');

    _data =
    {
        feed  : _dta.find('feed').text(),
        title : _dta.find('feed_title').text()
    };

    if(_data.feed.length==0) 
    {
        alert('Invalid Feed');
        return false;
    }

    if(my_template.subscribed_list.find('div.ch[feed="' + _data.feed + '"]').length==0)
    {
        _item = feed_item_c(_data.feed, _data.title, _flw);
        my_template.all_items_folder.after(_item.html() + "\n");
    }
}

function feed_add()
{
    var _data = { blog : my_blog.current ,
                  url  : my_template.feed_add_input.val() };

    do_request('POST', '/feed/quick', _data, feed_add_callback);
}

/* ARTICLES */

function update_right_header_title()
{
    var _title = "";

    if(my_feed.current)
    {
        _title = my_template.subscribed_list
            .find("div.ch[feed='" + my_feed.current + "']")
            .find('a').text();
        my_template.right_header_title.css('text-transform', 'none');
    }
    else
    {
        _title = "<?php echo $this->translation()->all_items ?>";
        my_template.right_header_title.css('text-transform', 'capitalize');
    }

    my_template.right_header_title.b_txtoverflow({ buffer: my_template.txtoverflow_buffer, width: (my_template.right_header_title.width() * 0.8), text: _title });
}

function article_populate(d, append)
{
    update_right_header_title();

    if(append==false)
    {
        my_template.article_list.html('');
        my_article.data = Array();
        my_article.current = null;
        my_article.older = 0;
        my_article.bottom = 0;
    }

    if(d.length==0)
    { 
        if(append==true) { my_article.older = 0; }
        return false; 
    }

    var _data   = null;
    var _item   = null;
    var _inner  = null;
    var _lsdata = Array();
    var _i      = 0;

    var _alw = my_template.article_list.width() * 0.6;

    d.each(function()
    {
        _data = 
        {
            feed                 : $(this).find('feed').text(),
            feed_title           : $(this).find('feed_title').text(),
            article              : $(this).find('article').text(),
            article_title        : $(this).find('article_title').text(),
            article_link         : $(this).find('article_link').text(),
            article_date         : $(this).find('article_date').text(),
            article_time         : $(this).find('article_time').text(),
            article_time_literal : $(this).find('article_time_literal').text(),
            article_date_local   : $(this).find('article_date_local').text(),
            article_author       : $(this).find('article_author').text(),
            article_content      : $(this).find('article_content').text(),
            publication_status   : $(this).find('publication_status').text(),
            entry                : $(this).find('entry').text()
        };

        if(my_article.data[_data.article]==undefined) // avoid dupl
        {
            _item  = my_template.article_blank.clone();
            _inner = _item.find('div.art');
            _inner.attr('feed', _data.feed);
            _inner.attr('article', _data.article);
            _inner.attr('entry', _data.entry);

            if(_data.publication_status.length>0)
            {
                _inner.find('div.arttog')
                    .removeClass('arttog-un')
                    .addClass('arttog-ck');
            }

            if(_data.feed_title.length==0)
            {
                _data.feed_title = 'Untitled';
            }

            _inner.find('span.artch').b_txtoverflow({ buffer: my_template.txtoverflow_buffer, width: _alw, text: _data.feed_title });
            _inner.find('span.arttt').b_txtoverflow({ buffer: my_template.txtoverflow_buffer, width: _alw, text: _data.article_title });
            _inner.find('div.artdte').text(_i < 5 ? _data.article_time_literal :
                                                    _data.article_date_local);
            _inner.find('div.artlnk > a').attr('href', _data.article_link);

            _lsdata[_i] = _item.html(); _i++;
        }

        my_article.data[_data.article] =
        {
            title   : _data.article_title,
            author  : _data.article_author,
            content : _data.article_content
        };
    });

    my_template.article_list.append(_lsdata.join("\n"));
    my_article.older = _data.article_time;

    if(my_article.display=='expanded') { article_show_all(); }
    if(append==false) { my_template.article_list.scrollTop(0); }

    my_article.bottom += my_template.article_list.find('div.art:last').position().top;
}

function article_list_callback(d)
{
    article_populate(d.find('articles').children(), (d.find('append').text()=="true"));
}

function article_list(older)
{
    var _url = ((my_feed.current) ? '/article/threaded' : '/article/all');

    var _data = { blog  : my_blog.current ,
                  feed  : my_feed.current ,
                  older : older };

    do_request('GET', _url, _data, article_list_callback);
}

function article_scroll_top()
{
    if(my_article.current)
    {
        my_template.article_list.animate(
        {
            scrollTop: my_article.current.position().top -
                my_template.article_list.position().top +
                my_template.article_list.scrollTop() - 2
        }, 200);
    }
}

function article_show_fix_vertical()
{
    var _rmh = my_template.right_middle_area.h / 2;
    var _rmt = my_template.right_middle_area.y;
    var _apt = my_article.current.position().top;
    var _coh = my_article.current.next('div.artview').outerHeight();

    var _scr = my_template.article_list.scrollTop() + 
               _apt -
               ((_coh > _rmh) ? _rmt : _rmh);

    my_template.article_list.animate({ scrollTop: _scr }, 200);
}

function article_hide(a)
{
    a.removeClass('art-op').next('div.artview').remove();
}

function article_hide_current()
{
    if(my_article.current) { article_hide(my_article.current); }
    my_article.current = null;
}

function article_hide_all()
{
    my_template.article_list.find('div.artview').remove();
    my_template.article_list.find('div.art').removeClass('art-op');
    my_article.current = null;
}

function article_show(a)
{
    if(my_article.data[a])
    {
        my_article.current = my_template.article_list
            .find("div.art[article='" + a + "']");

        if(my_article.current.hasClass('art-op')==false)
        {
            var _content = my_template.content_blank.clone();
            _content.find('h1').html(my_article.data[a].title);
            // _content.find('div.artbody').html(my_article.data[a].content + '<div style="clear:both"></div>'); /* div clear both for img float left in artbody css */
            _content.find('div.artbody').html(my_article.data[a].content); /* div clear both for img float left in artbody css */
            _content.find('div.artbody').find('a').attr('target', '_blank'); /* add target _blank to all links */
            my_article.current.after(_content.html()).addClass('art-op');
        }
    }
}

function article_show_all()
{
    article_hide_all();

    my_template.article_list.find('div.art').each(function()
    {
        var _art = $(this).attr('article');
        var _content = my_template.content_blank.clone();
        _content.find('h1').html(my_article.data[_art].title);
        _content.find('div.artbody').html(my_article.data[_art].content);
        $(this).after(_content.html()).addClass('art-op');
    });
}

function article_previous()
{
    var _prev = null;

    if(my_article.current)
    {
        _prev = my_article.current.prevAll('div.art').attr('article');
    }

    if(_prev) 
    { 
        if(my_article.display=='list') { article_hide_current(); }
        article_show(_prev); 
        article_scroll_top();
    }
}

function article_next()
{
    var _next = null;

    if(my_article.current)
    {
        _next = my_article.current.nextAll('div.art').attr('article');
    }
    else
    {
        _next = my_template.article_list.find('div.art').attr('article');
    }

    if(_next)
    {
        if(my_article.display=='list') { article_hide_current(); }
        article_show(_next);
        article_scroll_top();
    }
}

/* QUEUE */

function queue_add_callback(d)
{
    var _sel = "div.art" +
               "[feed='" + d.find('feed').text() + "']" + 
               "[article='" + d.find('article').text() + "']";

    my_template.article_list
        .find(_sel)
        .attr('entry', d.find('entry').text())
        .find('div.arttog')
        .removeClass('arttog-un')
        .addClass('arttog-ck')
        .blur();
}

function queue_add(f, a)
{
    var _data = { blog    : my_blog.current ,
                  feed    : f,
                  article : a };

    do_request('GET', '/queue/add', _data, queue_add_callback);
}

function queue_delete_callback(d)
{
    var _sel = "div.art[entry='" + d.find('entry').text() + "']";

    my_template.article_list.find(_sel)
        .find('div.arttog')
        .removeClass('arttog-ck')
        .addClass('arttog-un')
        .blur();
}

function queue_delete(e)
{
    var _data = { blog  : my_blog.current , entry : e };
    do_request('POST', '/queue/delete', _data, queue_delete_callback);
}

function on_blog_change()
{
    feed_list();
    article_list(null);
}

$(document).ready(function()
{
    my_template =
    {
        main_container       : $("#mainct"),
        //left_container       : $("#leftcontainer"),
        all_items            : $("#chall"),
        all_items_folder     : $("#challf"),
        //right_container      : $("#rightcontainer"),
        right_header_title   : $("#tplbartt"),
        right_middle         : $("#artlst"),
        article_list         : $("#artlst"),
        article_blank        : $("#articleblank"),
        content_blank        : $("#contentblank"),
        right_middle_area    : { x : 0 , y : 0 , w : 0 , h : 0 },
        right_middle_hover   : false,
        <?php if($this->browser_is_ie) : ?>
        LEFT_MIDDLE_OFFSET_TOP : 10,
        <?php else : ?>
        LEFT_MIDDLE_OFFSET_TOP : 20,
        <?php endif ?>
        //right_footer         : $("#rightfooter"),
        middle_menu          : $("#midmenu"),
        feed_add_lnk         : $("#chaddlnk"),
        feed_add_ct          : $("#chaddct"),
        feed_add_input       : $("#chaddinput"),
        feed_add_button      : $("#chaddbtn"),
        feed_add_cancel      : $("#chaddccl"),
        subscribed_list      : $("#chlst"),
        feed_item_blank      : $("#feeditemblank"),
        article_expanded_lnk : $("#articleexpandedlnk"),
        article_expanded_lab : $("#articleexpandedlab"),
        article_list_lnk     : $("#articlelistlnk"),
        article_list_lab     : $("#articlelistlab"),
        article_prev         : $("#articleprev"),
        article_next         : $("#articlenext"),
        txtoverflow_buffer   : $("#b_txtoverflow-buffer"),
        no_feed_message      : $("#nofeedmsg")
    }; 
    
    function window_update()
    {
        var _w = { height : $(window).height(),
                   width  : $(window).width() };

        my_template.right_middle.height(_w.height - 
                                        my_template.right_middle.position().top);

        my_template.subscribed_list.height(_w.height - 
                                           my_template.subscribed_list.offset().top -
                                           my_template.LEFT_MIDDLE_OFFSET_TOP);

        my_template.right_middle_area.x = my_template.right_middle.offset().left;
        my_template.right_middle_area.y = my_template.right_middle.offset().top;
        my_template.right_middle_area.w = my_template.right_middle.width();
        my_template.right_middle_area.h = my_template.right_middle.height();
    }

    function browser_fix()
    {
        if(jQuery.browser.opera)
        {
            my_template.article_prev.css('margin-top', 10);
            my_template.article_next.css('margin-top', 10);
        }
    }

    function no_blog()
    {
        //document.location='/site';
    }

    function initialize()
    {
        <?php if(count($this->blogs)==0) : ?>
        no_blog(); // tutorial
        <?php endif ?>
        set_article_display();
        feed_list();
        article_list(null);
        browser_fix();
        window_update();
    }

    /* events */

    my_template.feed_add_lnk.click(function()
    {
        if(my_template.feed_add_ct.toggle().is(':visible'))
        {
            my_template.feed_add_input.val('');
            my_template.feed_add_input.focus();
        }
        $(this).blur();
        return false;
    });

    my_template.feed_add_button.click(function()
    {
        feed_add();
        my_template.feed_add_ct.hide();
        $(this).blur();
        return false;
    });

    my_template.feed_add_cancel.click(function()
    {
        my_template.feed_add_ct.hide();
        $(this).blur();
        return false;
    });

    my_template.feed_add_input.keypress(function(e)
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            my_template.feed_add_button.click();
        }
    });

    my_template.article_list_lnk.click(function()
    {
        if(active_request==false)
        {
            my_article.display = 'list';
            save_setting('article', 'display', my_article.display);
        }
        return false;
    });

    my_template.article_expanded_lnk.click(function()
    {
        if(active_request==false)
        {
            my_article.display = 'expanded';
            save_setting('article', 'display', my_article.display);
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
        if(my_template.right_middle_hover==true && $.browser.msie) // Emulate wheel scroll on IE
        {
            var j = null;
            e = e ? e : window.event;
            j = e.detail ? e.detail * -1 : e.wheelDelta / 2;
            my_template.right_middle.scrollTop(my_template.right_middle.scrollTop() - j);
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

    my_template.right_middle.scroll(function()
    {
        // if(my_template.article_list.scrollTop() > (my_article.bottom * 2/3) && /* fails when threaded */
        if(my_template.article_list.scrollTop() > (my_article.bottom / 2) &&
           my_article.older > 0 && active_request==false)
        {
            article_list(my_article.older);
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

        my_template.right_middle_hover = mouse_is_over_area(_mp.x, _mp.y, my_template.right_middle_area);
    }

    $(window).bind('mousemove', function(e) /* Mozilla */
    {
        on_mouse_move(e);
    }); 

    window.onmousemove = document.onmousemove = on_mouse_move; /* IE */

    /* controls */

    my_template.all_items.click(function()
    {
        my_feed.current = null;
        article_list(null);
        return false;
    });

    my_template.subscribed_list.find('a.feeditemlnk').live('click', function()
    {
        my_feed.current = $(this).parent().attr('feed');
        article_list(null);
        $(this).blur();
        return false;
    });

    my_template.article_list.find('div.art')
        .find('div.artlab')
        .live('click', function()
    {
        var _pt = $(this).parent();

        if(_pt.hasClass('art-op'))
        {
            article_hide_current();
            $(this).blur();
            return false; 
        }

        if(my_article.display=='list')
        {
            article_hide_current();
            article_show(_pt.attr('article'));
            article_show_fix_vertical();
        }

        $(this).blur();
        return false;
    });

    my_template.article_list.find('div.art')
        .find('div.arttog')
        .live('click', function()
    {
        var _i = $(this).parent();

        if($(this).hasClass('arttog-ck'))
        {
            queue_delete(_i.attr('entry'));
        }
        else
        {
            queue_add(_i.attr('feed'), _i.attr('article'));
        }

        return false;
    });

    my_template.article_next.click(function()
    {
        article_next();
        $(this).blur();
    });

    my_template.article_prev.click(function()
    {
        article_previous();
        $(this).blur();
    });

    /* initialize */

    $(document).bind('blog_changed' , function(e)
    {
        on_blog_change();
    });

    initialize();
});
