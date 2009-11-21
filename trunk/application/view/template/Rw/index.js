var my_template = null;

var my_feed =
{
    current : null,
    type    : 'all',
    title   : ""
};

var my_article = 
{
    display : "<?php echo $this->settings->article->display ?>",
    data    : Array(),
    current : null,
    older   : 0,
    bottom  : 0
};


function set_article_display()
{
    if(my_article.display=='list')
    {
        my_template.article_expanded_lab.hide();
        my_template.article_expanded_lnk.show();
        my_template.article_list_lnk.hide();
        my_template.article_list_lab.show();
        article_hide_all();
    }
    if(my_article.display=='expanded')
    {
        my_template.article_expanded_lnk.hide();
        my_template.article_expanded_lab.show();
        my_template.article_list_lab.hide();
        my_template.article_list_lnk.show();
        article_show_all();
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

function tag_item_c(tag, w)
{
    var _item = null;
    var _inner = null;

    _item = my_template.tag_item_blank.clone();
    _inner = _item.find('div.ch');
    _inner.attr('tag', tag);
    _inner.find('a.tagitemlnk').attr('title', tag).b_txtoverflow({ buffer: my_template.txtoverflow_buffer, width: w, text: tag });

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

function tag_populate(d)
{
    if(d.length==0) { return false; }

    var _flw = my_template.subscribed_list.width() * 0.9;

    d.each(function()
    {
        _data =
        {
            tag_name  : $(this).find('tag_name').text(),
            tag_feeds : $(this).find('tag_feeds').children()
        };

        _item = tag_item_c(_data.tag_name, _flw);
        my_template.subscribed_list.append(_item.html() + "\n");
        feed_populate(_data.tag_feeds);
    });
}

function no_feed()
{
    my_template.no_feed_message.show();
}

function feed_list_callback(d)
{
    var _fl = d.find('feeds').children();
    // if(_fl.length==0) { no_feed(); } // tutorial
    feed_populate(_fl);
    tag_populate(d.find('tags').children()); // tags
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
        my_template.subscribed_all_folder.after(_item.html() + "\n");
    }
}

function feed_add()
{
    var _data = { blog : my_blog.current ,
                  url  : my_template.feed_add_input.val() };

    do_request('POST', '/feed/quick', _data, feed_add_callback);
}

/* ARTICLES */

function update_right_header_title(t)
{
    my_template.right_header_title.b_txtoverflow({ buffer: my_template.txtoverflow_buffer, width: (my_template.right_header_title.width() * 0.8), text: t });
}

function article_populate(d, append)
{
    if(append==false)
    {
        my_template.right_middle.html('');
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

    var _alw = my_template.right_middle.width() * 0.6;

    d.each(function()
    {
        _data = 
        {
            feed                 :  $(this).find('feed').text(),
            feed_title           :  $(this).find('feed_title').text(),
            article              :  $(this).find('article').text(),
            article_title        :  $(this).find('article_title').text(),
            article_link         :  $(this).find('article_link').text(),
            article_date         :  $(this).find('article_date').text(),
            article_time         :  $(this).find('article_time').text(),
            article_time_literal :  $(this).find('article_time_literal').text(),
            article_date_local   :  $(this).find('article_date_local').text(),
            article_author       :  $(this).find('article_author').text(),
            article_content      :  $(this).find('article_content').text(),
            publication_status   :  $(this).find('publication_status').text(),
            entry                :  $(this).find('entry').text(),
            wr                   : ($(this).find('wr').text()=='1')
        };

        if(my_article.data[_data.article]==undefined) // avoid dupl
        {
            _item  = my_template.article_blank.clone();
            _inner = _item.find('div.art');
            _inner.attr('feed', _data.feed);
            _inner.attr('article', _data.article);
            _inner.attr('entry', _data.entry);

            if(_data.wr)
            {
                _inner.addClass('art-wr');
            }

            if(_data.publication_status.length>0)
            {
                _inner.find('div.arttog')
                    .removeClass('arttog-un')
                    .addClass('arttog-ck');
            }

            if(_data.article_title.length==0)
            {
                _data.article_title = 'Untitled';
            }

            _inner.find('span.artch').b_txtoverflow({ buffer: my_template.txtoverflow_buffer, width: _alw, text: _data.feed_title });
            _inner.find('span.arttt').addClass((_data.wr ? 'arttt-wr' : '')).b_txtoverflow({ buffer: my_template.txtoverflow_buffer, width: _alw, text: _data.article_title });
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

    my_template.right_middle.append(_lsdata.join("\n"));
    my_article.older = _data.article_time;

    if(my_article.display=='expanded') { article_show_all(); }
    if(append==false) { my_template.right_middle.scrollTop(0); }

    my_article.bottom += my_template.right_middle.find('div.art:last').position().top;
}

function writing_populate(d, append)
{
    if(append==false)
    {
        my_template.right_middle.html('');
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

    var _alw = my_template.right_middle.width() * 0.6;

    d.each(function()
    {
        _data = 
        {
            writing              :  $(this).find('writing').text(),
            writing_title        :  $(this).find('writing_title').text(),
            writing_content      :  $(this).find('writing_content').text(),
            writing_date         :  $(this).find('writing_date').text(),
            writing_date_time    :  $(this).find('writing_date_time').text(),
            writing_date_literal :  $(this).find('writing_date_literal').text(),
            writing_date_local   :  $(this).find('writing_date_local').text(),
            publication_status   :  '' // TODO
        };

        if(my_article.data[_data.writing]==undefined) // avoid dupl
        {
            _item  = my_template.writing_blank.clone();
            _inner = _item.find('div.wtg');
            _inner.attr('writing', _data.writing);

            if(_data.publication_status.length>0)
            {
                _inner.find('div.wtgtog')
                    .removeClass('wtgtog-un')
                    .addClass('wtgtog-ck');
            }

            if(_data.writing_title.length==0)
            {
                _data.writing_title = 'Untitled';
            }

            _inner.find('span.wtgtt').b_txtoverflow({ buffer: my_template.txtoverflow_buffer, width: _alw, text: _data.writing_title });
            _inner.find('div.wtgdte').text(_i < 5 ? _data.writing_time_literal :
                                                    _data.writing_date_local);

            _lsdata[_i] = _item.html(); _i++;
        }

        my_article.data[_data.writing] =
        {
            title   : _data.writing_title,
            author  : '',
            content : _data.writing_content
        };
    });

    my_template.right_middle.append(_lsdata.join("\n"));
    my_article.older = _data.writing_time;

    if(my_article.display=='expanded') { article_show_all(); }
    if(append==false) { my_template.right_middle.scrollTop(0); }

    my_article.bottom += my_template.right_middle.find('div.wtg:last').position().top;
}

function article_list_callback(d)
{
    var _lst = null;
    var _app = null;

    _lst = d.find('articles');
    _app = d.find('append');

    if(_lst.length>0) 
    { 
        article_populate(_lst.children(), (_app.text()=="true")); 
        return true;
    }

    _lst = d.find('writings')

    if(_lst.length>0)
    {
        writing_populate(_lst.children(), (_app.text()=="true"));
        return true;
    }
}

function article_list(older)
{
    var _data = { blog  : my_blog.current ,
                  older : older };
    if(my_feed.current) { _data[my_feed.type] = my_feed.current; }

    do_request('GET', ('/article/' + my_feed.type), _data, article_list_callback);
}

function article_scroll_top()
{
    if(my_article.current)
    {
        var _scr = my_article.current.position().top -
                   my_template.right_middle.position().top +
                   my_template.right_middle.scrollTop() - 
                   (my_article.display=='list' ? 2 : 10);

        my_template.scroll_animate=true;
        my_template.right_middle.animate({ scrollTop: _scr }, 200, function() { my_template.scroll_animate=false; });
    }
}

function article_show_fix_vertical()
{
    var _rmh = my_template.right_middle_area.h / 2;
    var _rmt = my_template.right_middle_area.y;
    var _apt = my_article.current.position().top;
    var _coh = my_article.current.next('div.artview').outerHeight();

    var _scr = my_template.right_middle.scrollTop() + 
               _apt -
               ((_coh > _rmh) ? _rmt : _rmh);

    my_template.scroll_animate=true;
    my_template.right_middle.animate({ scrollTop: _scr }, 200, function() { my_template.scroll_animate=false });
}

function article_hide(a)
{
    article_blur();
    a.removeClass('art-op').next('div.artview').remove();
}

function article_hide_current()
{
    if(my_article.current) { article_hide(my_article.current); }
    //my_article.current = null;
}

function article_hide_all()
{
    my_template.right_middle.find('div.artview').remove();
    my_template.right_middle.find('div.artview-sep').remove();
    my_template.right_middle.find('div.art').removeClass('art-op').removeClass('art-op-focus');
    my_article.current = null;
}

function article_focus_callback(d)
{
    return false;
}

function article_focus()
{
    if(my_article.current)
    {
        my_article.current.addClass('art-op-focus');
        my_article.current.next('div.artview').addClass('artview-focus');

        var _data = { blog     : my_blog.current ,
                      article  : my_article.current.attr('article') };

        $.ajax({ type: 'POST', url: '/rw/wr', dataType: "xml", data: _data });
        my_article.current.addClass('art-wr').find('span.arttt').addClass('arttt-wr');
    }
}

function article_blur()
{
    if(my_article.current)
    {
        my_article.current.removeClass('art-op-focus');
        my_article.current.next('div.artview').removeClass('artview-focus');
    }
}

function article_show(a)
{
    if(my_article.data[a])
    {
        my_article.current = my_template.right_middle
            .find("div.art[article='" + a + "']");

        if(my_article.current.hasClass('art-op')==false)
        {
            var _content = my_template.content_blank.clone();
            _content.find('h1').html(my_article.data[a].title);
            _content.find('div.artbody').html(my_article.data[a].content); /* div clear both for img float left in artbody css */
            _content.find('div.artbody').find('a').attr('target', '_blank'); /* add target _blank to all links */
            my_article.current.after(_content.html()).addClass('art-op');
        }

        article_focus();
    }
}

function article_show_all()
{
    article_hide_all();

    my_template.right_middle.prepend('<div class="artview-sep">&nbsp;</div>');
    my_template.right_middle.find('div.art').each(function()
    {
        var _art = $(this).attr('article');
        var _content = my_template.content_blank.clone();
        _content.find('h1').html(my_article.data[_art].title);
        _content.find('div.artbody').html(my_article.data[_art].content);
        $(this).after('<div class="artview-sep">&nbsp;</div>');
        $(this).after(_content.html()).addClass('art-op');
    });

    article_show(my_template.right_middle.find('div.art').eq(0).attr('article'));
}

function article_previous()
{
    var _prev = null;

    if(my_article.current)
    {
        article_blur();
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
        article_blur();
        _next = my_article.current.nextAll('div.art').attr('article');
    }
    else
    {
        _next = my_template.right_middle.find('div.art').attr('article');
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

    my_template.right_middle
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

    my_template.right_middle.find(_sel)
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


function writing_editor_init()
{
    CKEDITOR.replace('writingbody',
    {
        toolbar : [ [ 'Source', '-', 'Bold', 'Italic' ] ],
        height: ($(window).height() - 350),
        toolbarCanCollapse : false,
        resize_enabled : false,
        contentsCss : '/css/ck_content.css'
    });
}

function writing_edit(w)
{
    var _rect = { L : $(window).width()  * 0.1 ,
                  T :                       50 ,
                  W : $(window).width()  * 0.8 ,
                  H : $(window).height()  -100 };

    my_template.edit_form.b_modal();

    var _edit_title = '';
    var _edit_content = '';

    if(my_article.data[w])
    {
        _edit_title = my_article.data[w].title;
        _edit_title = my_article.data[w].content;
        my_article.current = my_template.right_middle.find("div.art[article='" + w + "']");
    }

    my_template.edit_form.find('div.form-bot').css('top', _rect.H - 55); // position hack
    my_template.edit_form.find("input[name='writingtitle']").val(_edit_title).focus();
    CKEDITOR.instances.writingbody.setData(_edit_content);
}

function writing_save_callback(d)
{
    // TODO
    my_template.edit_form.b_modal_close();
    flash_message("<?php echo $this->translation()->saved ?>");
}

function writing_save_current()
{
    var _data =
    {
        blog            : my_blog.current ,
        writing         : null, // TODO 
        writing_title   : my_template.edit_form.find("input[name='writingtitle']").val(),
        writing_content : CKEDITOR.instances.writingbody.getData()
    };

    do_request('POST', '/writing/save', _data, writing_save_callback);
}


$(document).ready(function()
{
    my_template =
    {
        main_container       : $("#mainct"),
        //left_container       : $("#leftcontainer"),
        subscribed_all       : $("#chall"),
        subscribed_all_folder: $("#challf"),
        writings_all         : $("#wrall"),
        //right_container      : $("#rightcontainer"),
        right_header_title   : $("#tplbartt"),
        right_middle         : $("#artlst"),
        article_blank        : $("#articleblank"),
        writing_blank        : $("#writingblank"),
        content_blank        : $("#contentblank"),
        right_middle_area    : { x : 0 , y : 0 , w : 0 , h : 0 },
        right_middle_hover   : false,
        <?php if($this->browser_is_ie) : ?>
        LEFT_MIDDLE_OFFSET_TOP : 10,
        <?php else : ?>
        LEFT_MIDDLE_OFFSET_TOP : 20,
        <?php endif ?>
        LEFT_MIDDLE_V_MARGIN : 70,
        WRITINGS_MENU_HEIGHT : 30, 
        feed_add_lnk         : $("#chaddlnk"),
        feed_add_ct          : $("#chaddct"),
        feed_add_input       : $("#chaddinput"),
        feed_add_button      : $("#chaddbtn"),
        feed_add_cancel      : $("#chaddccl"),
        writing_add_lnk      : $("#wraddlnk"),
        subscribed_menu      : $("#subscribed"),
        subscribed_list      : $("#chlst"),
        writings_menu        : $("#writings"),
        writings_list        : $("#wrlst"),
        feed_item_blank      : $("#feeditemblank"),
        tag_item_blank       : $("#tagitemblank"),
        article_expanded_lnk : $("#articleexpandedlnk"),
        article_expanded_lab : $("#articleexpandedlab"),
        article_list_lnk     : $("#articlelistlnk"),
        article_list_lab     : $("#articlelistlab"),
        article_prev         : $("#articleprev"),
        article_next         : $("#articlenext"),
        edit_form            : $("#editform"),
        txtoverflow_buffer   : $("#b_txtoverflow-buffer"),
        no_feed_message      : $("#nofeedmsg"),
        scroll_animate       : false
    }; 
    
    function window_update()
    {
        var _w = { height : $(window).height(),
                   width  : $(window).width() };

        my_template.right_middle.height(_w.height - 
                                        my_template.right_middle.position().top);

        var _lmh = _w.height - my_template.subscribed_list.offset().top -
                               my_template.LEFT_MIDDLE_OFFSET_TOP;

        my_template.subscribed_list.height(_lmh - (my_template.LEFT_MIDDLE_V_MARGIN + 
                                                   my_template.WRITINGS_MENU_HEIGHT));
        my_template.writings_menu.css('top', my_template.subscribed_menu.position().top + 
                                             _lmh - my_template.WRITINGS_MENU_HEIGHT);
        my_template.writings_list.height(my_template.WRITINGS_MENU_HEIGHT);

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
        // no_blog(); // tutorial
        <?php endif ?>
        set_article_display();
        feed_list();
        article_list(null);
        browser_fix();
        window_update();
        writing_editor_init();
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

    my_template.writing_add_lnk.click(function()
    {
        writing_edit(null);
        $(this).blur();
        return false;
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
        if(my_template.right_middle.scrollTop() > (my_article.bottom / 2) &&
           my_article.older > 0 && active_request==false)
        {
            article_list(my_article.older);
        }

        /* article expanded scroll focus */

        if(my_article.display=='expanded')
        {
            var _found = null;
            my_template.right_middle.find('div.art').each(function()
            {
                if(_found==null)
                {
                    _found=$(this).position().top;
                    _found=((_found<my_template.right_middle_area.y || 
                             _found>my_template.right_middle_area.h) ? null : $(this));
                }
            });
            if(_found)
            {
                var _found_art = $(this).attr('article');
                var _current_art = (my_article.current ? my_article.current.attr('article') : null);
                if(_found_art!=_current_art && my_template.scroll_animate==false)
                {
                    article_blur();
                    my_article.current=_found;
                    article_focus();
                }
            }
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

    my_template.subscribed_all.click(function()
    {
        my_feed.current = null;
        my_feed.type    = 'all';
        article_list(null);
        update_right_header_title($(this).attr('title'));
        $(this).blur();
        return false;
    });

    my_template.subscribed_list.find('a.feeditemlnk').live('click', function()
    {
        my_feed.current = $(this).parent().attr('feed');
        my_feed.type    = 'feed';
        article_list(null);
        update_right_header_title($(this).attr('title'));
        $(this).blur();
        return false;
    });

    my_template.subscribed_list.find('a.tagitemlnk').live('click', function()
    {
        my_feed.current = $(this).parent().attr('tag');
        my_feed.type    = 'tag';
        article_list(null);
        update_right_header_title($(this).attr('title'));
        $(this).blur();
        return false;
    });

    my_template.writings_all.click(function()
    {
        my_feed.current = null;
        my_feed.type    = 'writing';
        article_list(null);
        update_right_header_title($(this).attr('title'));
        $(this).blur();
        return false;
    });

    my_template.right_middle.find('div.art')
        .find('div.artlab')
        .live('click', function()
    {
        var _pt = $(this).parent();

        if(my_article.display=='list')
        {
            if(_pt.hasClass('art-op'))
            {
                article_hide_current();
                $(this).blur();
                return false; 
            }

            article_hide_current();
        }
        
        if(my_article.display=='expanded')
        {
            article_blur();
        }

        article_show(_pt.attr('article'));
        article_show_fix_vertical();

        $(this).blur();
        return false;
    });

    my_template.right_middle.find('div.art')
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

    my_template.edit_form
        .find("button[name='editformsave']")
        .live('click', function()
    {
        if(active_request==true) { return false; }
        writing_save_current();
    });

    my_template.edit_form
        .find("button[name='editformcancel']")
        .live('click', function()
    {
        if(active_request==true) { return false; }
        my_template.edit_form.b_modal_close();
    });

    /* initialize */

    $(document).bind('blog_changed' , function(e)
    {
        on_blog_change();
    });

    initialize();
});
