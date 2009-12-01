var my_template = null;

var my_queue = 
{
    data        : Array() , // entry data
    objects     : Array() , // entry DOM objects
    check       : Array() , // entry status check queue
    current     : null    ,
    scroll      : 0       ,
    editor      : null    ,
    sorting     : false   ,
    publication : null    ,
    interval    : 0       ,
    request     : false
};

var entry_check_update_interval = 10000;

function entry_set_status(e, s)
{
    if(typeof e == 'string' && e.length > 0)
    {
        e = my_queue.objects[e];
    }

    if(typeof e == 'object')
    {
        e.attr('status', s);
        var _e = e.attr('entry');

        if(s=='<?php echo BlogEntry::STATUS_WORKING ?>')
        {
            e.find('div.etydte').text('publishing...');
        }
        if(s=='<?php echo BlogEntry::STATUS_FAILED ?>')
        {
            e.addClass('ety-fail');
            e.find('div.etylab').append('<nobr><img src="/image/warning.png"/></nobr>');
        }
        if(s=='<?php echo BlogEntry::STATUS_PUBLISHED ?>')
        {
            e.addClass('ety-pub');
            e.find('div.entrydndhdr').addClass('entrydndhdr-pb');
            e.find('div.etytog').replaceWith('<div class="etytog-pb">P</div>');
            e.find('div.etyedlnk').remove();
            e.find('div.etydte').text('published');

            if(e.is(':visible')) /* move to bottom */
            {
                e.fadeOut(100, function() 
                { 
                   my_template.entry_list.append($(this).clone().fadeIn(100));
                   $(this).remove();
                });
            }
        }
    }
}

function entry_cache_init()
{
    my_template.entry_list.find('div.ety').each(function()
    {
        my_queue.objects[$(this).attr('entry')] = $(this);
    });
}

function entry_check_callback(d)
{
    var _updata = null;

    d.find('result').children().each(function()
    {
        _updata = 
        {
            entry  : $(this).find('entry').text(),
            status : $(this).find('status').text(),
            time   : parseInt($(this).find('publication_date_diff').text())
        };

        my_queue.data[_updata.entry].status = _updata.status;
        my_queue.data[_updata.entry].time = _updata.time;
        entry_set_status(_updata.entry, _updata.status);

        if((_updata.status=='<?php echo BlogEntry::STATUS_WAITING ?>' || 
            _updata.status=='<?php echo BlogEntry::STATUS_WORKING ?>')==false)
        {
            entry_check_remove(_updata.entry);
        }
    });
}

function entry_check_init()
{
    setTimeout('entry_check()', entry_check_update_interval);
}

function entry_check()
{
    if(my_queue.check.length>0)
    {
        var _data = { blog : my_blog.current, waiting : my_queue.check.join(',') };
        do_request('GET', '/queue/check', _data, entry_check_callback);
    }

    entry_check_init();
}

function entry_check_index(e)
{
    var i;
    var j=null;

    for(i=0;i<my_queue.check.length;i++)
    {
        if(my_queue.check[i]==e) { j=e; }
    }

    return j;
}

function entry_check_add(e)
{
    if(entry_check_index(e)==null)
    {
        my_queue.check.push(e);
    }
}

function entry_check_remove(e)
{
    var j=entry_check_index(e);

    if(j!=null)
    {
        my_queue.check.splice(j, 1);
    }
}

function entry_updater_init()
{
    return setTimeout('entry_updater()', 1000);
}

function entry_updater()
{
    my_template.entry_list.find('div.ety').each(function()
    {
        var _e = $(this).attr('entry');

        /* object is destroyed during sortable */
        if(my_queue.data[_e]==undefined) { return false; }

        var _d = $(this).find('div.etydte');

        if(my_queue.data[_e].time<=0 && 
           (my_queue.data[_e].status=='<?php echo BlogEntry::STATUS_WAITING ?>' || 
            my_queue.data[_e].status=='<?php echo BlogEntry::STATUS_WORKING ?>'))
        {
            _d.text('publishing...');
            entry_check_add(_e);
            return true;
        }

        _d.pc_literalTime({ t : my_queue.data[_e].time });

        if(my_queue.data[_e].status!='<?php echo BlogEntry::STATUS_IDLE ?>')
        {
            my_queue.data[_e].time-=1;
        }
    });

    entry_updater_init();
}

function entry_populate(d)
{
    if(d.length==0) { return false; }

    var _data   = null;
    var _item   = null;
    var _inner  = null;
    var _lsdata = Array();
    var _i      = 0;

    var _alw = my_template.entry_list.width() * 0.6;

    d.each(function()
    {
        _data = 
        {
            entry    : $(this).find('entry').text(),
            title    : $(this).find('title').text(),
            content  : $(this).find('content').text(),
            status   : $(this).find('status').text(),
            time     : parseInt($(this).find('time').text()),
            ordering : parseInt($(this).find('ordering').text()),
            link     : $(this).find('link').text()
        };

        if(my_queue.data[_data.entry]==undefined) // avoid dupl
        {
            my_queue.data[_data.entry]=_data;

            _item  = my_template.entry_blank.clone();
            _inner = _item.find('div.ety');
            _inner.attr('entry', _data.entry);

            (_data.link.length>0) ?
                _inner.find('div.artlnk > a').attr('href', _data.link) :
                _inner.find('div.artlnk').hide();

            entry_set_status(_inner, _data.status);

            if(_data.title.length==0)
            {
                _data.title = 'Untitled';
            }

            _inner.find('span.etytt').b_txtoverflow({ buffer: my_template.txtoverflow_buffer, width: _alw, text: _data.title });

            _lsdata[_i] = _item.html(); _i++;
        }
    });

    var _pls = my_template.entry_list.find('div.ety[status="published"]').eq(0);

    if(_pls.length>0)
    {
        _pls.before(_lsdata.join("\n"));
    }
    else
    {
       my_template.entry_list.append(_lsdata.join("\n"));
    }

    entry_sortable_init();
    entry_cache_init();
    my_template.entry_list.scrollTop(my_queue.scroll);
}

function entry_list_callback(d)
{
    my_queue.data    = Array();
    my_queue.objects = Array();
    my_queue.check   = Array();
    my_queue.current = null;
    my_queue.scroll  = my_template.entry_list.scrollTop()
    my_template.entry_list.html('');

    var _qr = d.find('result').find('queue').children();
    var _pr = d.find('result').find('published').children();

    my_template.no_entry_tutorial.hide();

    if((_qr.length + _pr.length)==0)
    { 
        my_template.no_entry_tutorial.show();
    }
    else
    {
        entry_populate(_qr);
        entry_populate(_pr);
    }
}

function entry_list()
{
    do_request('GET', '/queue/list', { blog : my_blog.current }, entry_list_callback);
}

function entry_show_fix_vertical()
{
    var _rmh = my_template.queue_middle_area.h / 2;
    var _rmt = my_template.queue_middle_area.y;
    var _apt = my_queue.current.position().top;
    var _coh = my_template.entry_list.find('div.etyview').outerHeight();

    var _scr = my_template.entry_list.scrollTop() + 
               _apt -
               ((_coh > _rmh) ? _rmt : _rmh);

    my_template.entry_list.animate({ scrollTop: _scr }, 200);
}

function entry_hide(e)
{
    e.removeClass('ety-op');
    my_template.entry_list.find('div.etyview').remove();
    e.find('div.entrydndhdr').css('opacity', 1);
    //e.removeClass('ety-op').next('div.etyview').remove();
    //e.removeClass('ety-op').next('div.etyform').remove();
    //$("#my_source_editor").remove();
}

function entry_hide_current()
{
    if(my_queue.current) { entry_hide(my_queue.current); }
    my_queue.current = null;
}

function entry_show(e)
{
    if(my_queue.data[e])
    {
        my_queue.current = my_queue.objects[e];

        var _op = my_queue.current.hasClass('ety-op');
        var _content = _op ?
            _content = my_template.entry_list.find('div.etyview') : 
                       my_template.content_blank.clone();

        my_queue.current.find('div.entrydndhdr').css('opacity', 0.5);

        _content.find('h1').html(my_queue.data[e].title );
        _content.find('div.etybody').html(my_queue.data[e].content);
        /* add target _blank to all links */
        _content.find('div.etybody').find('a').attr('target', '_blank');

        if(my_queue.current.hasClass('ety-op')==false)
        {
            my_queue.current.after(_content.html()).addClass('ety-op');
        }
    }
}

function queue_editor_init()
{
    CKEDITOR.replace('entrybody', 
    { 
        toolbar : [ [ 'Source', '-', 'Bold', 'Italic' ] ],
        height: ($(window).height() - 350),
        toolbarCanCollapse : false,
        resize_enabled : false,
        contentsCss : '/css/ck_content.css'
    });
}

function entry_edit(e)
{
    if(my_queue.data[e])
    {
        my_queue.current = my_queue.objects[e];

        var _rect = { L : $(window).width()  * 0.1 , 
                      T :                       50 ,
                      W : $(window).width()  * 0.8 , 
                      H : $(window).height()  -100 };

        my_template.edit_form.b_modal();

        my_template.edit_form.find('div.form-bot').css('top', _rect.H - 55); // position hack
        my_template.edit_form.find("input[name='entrytitle']").val(my_queue.data[e].title).focus();
        CKEDITOR.instances.entrybody.setData(my_queue.data[e].content)
    }
}

function entry_save_callback(d)
{
    var _e = d.find('entry').text();
    my_queue.data[_e].title = d.find('title').text();
    my_queue.data[_e].content = d.find('content').text();
    my_queue.current.find('span.etytt').text(my_queue.data[_e].title);
    my_template.edit_form.b_modal_close();
    entry_show(_e);
    flash_message("<?php echo $this->translation()->saved ?>");
}

function entry_save_current()
{
    var _data = 
    { 
        blog    : my_blog.current , 
        entry   : my_queue.current.attr('entry'),
        title   : my_template.edit_form.find("input[name='entrytitle']").val(),
        content : CKEDITOR.instances.entrybody.getData()
    };

    do_request('POST', '/queue/update', _data, entry_save_callback);
}

function entry_delete_callback(d)
{
    var _e = d.find('entry').text();
    my_queue.objects[_e].remove();
    entry_check_remove(_e);
}

function entry_delete(e)
{
    var _data = { blog  : my_blog.current , entry : e };
    entry_hide_current();
    do_request('POST', '/queue/delete', _data, entry_delete_callback);
}

function entry_position(e, p)
{
    var _data = { blog  : my_blog.current , entry : e, position: p };
    do_request('POST', '/queue/position', _data, entry_list_callback);
}

function entry_sortable_callback(e)
{
    var _p = 1;

    my_template.entry_list.find('div.ety').each(function()
    {
        if(e==$(this).attr('entry') && _p != my_queue.data[e].ordering)
        {
            entry_position(e, _p);
        }
    
        _p++;
    });
}

function entry_sortable_init()
{
    my_template.entry_list.sortable(
    {
        handle : "div.entrydndhdr",
        items : "div.ety[status!='published']",
        cancel : "div.ety-op",
        distance : 10,
        start: function(e,u)
        {
            u.item.find('div.entrydndhdr').css('opacity', 0.5);
            //entry_hide_current();
            my_queue.sorting = true;
        },
        update: function (e,u)
        {
            entry_sortable_callback(u.item.attr('entry'));
        }
    });
    my_template.entry_list.disableSelection();
}

function toggle_queue_publication()
{
    my_queue.publication = my_queue.publication ^ true;
    blog_update('publication_auto', (my_queue.publication ? 1 : 0));
}

function set_queue_publication()
{
    if(my_queue.publication==null)
    {
        my_queue.publication = (my_blog.info['publication_auto']=='true');
    }

    if(my_queue.publication)
    {
        my_template.queue_pub_play.hide();
        my_template.queue_pub_pause.show();
    }
    else
    {
        my_template.queue_pub_pause.hide();
        my_template.queue_pub_play.show();
    }
}

function set_queue_publication_auto()
{
    var _data = { blog        : my_blog.current   ,
                  interval    : my_queue.interval ,
                  publication : (my_queue.publication ? 1 : 0) };

    do_request('POST', '/queue/auto', _data, entry_list_callback);
}

function set_queue_interval()
{
    my_queue.interval = parseInt(my_blog.info['publication_interval']);

    my_template.queue_interval_sel.find('option').each(function()
    {
        if($(this).val()==my_queue.interval)
        {
            $(this).attr('selected', true);
        }
    });
}

function set_queue_header_title()
{
    var _tts = my_template.queue_toolbar.position().left - 
               my_template.queue_header_title.position().left -
               80;

    my_template.queue_header_title.find('span').b_txtoverflow({ buffer: my_template.txtoverflow_buffer, width: _tts, text: (' - ' + my_template.blog_list.find('option:selected').text()) });
}

function initialize()
{
    my_queue.data        = Array();
    my_queue.current     = null;
    my_queue.sorting     = false;
    my_queue.publication = null;
    my_queue.interval    = 0;

    set_queue_header_title();
    set_queue_publication();
    set_queue_interval();
    entry_list();
    entry_updater_init();
    entry_check_init();
}

function on_blog_change()
{
    blog_load();
}

function on_blog_load()
{
    initialize();
}

function confirmation_send_cb(d)
{
    alert('Email sent, please check your inbox.');
    my_template.confirmation_form.b_modal_close();
}

function confirmation_send()
{
    do_request('POST', '/profile/resend', { }, confirmation_send_cb);
}

$(document).ready(function()
{
    my_template =
    {
        main_container     : $("#mainct"),
        blog_list          : $("#bloglstsel"),
        queue_header       : $("#tplbar"),
        queue_header_title : $("#tplbartt"),
        queue_toolbar      : $("#tplbaropt"),
        queue_middle       : $("#etylst"),
        entry_list         : $("#etylst"),
        entry_blank        : $("#entryblank"),
        content_blank      : $("#contentblank"),
        edit_form          : $("#editform"),
        confirmation_form  : $("#confirmationform"),
        confirmation_send  : $("#confirmationsend"),
        confirmation_ccel  : $("#confirmationcancel"),
        queue_middle_area  : { x : 0 , y : 0 , w : 0 , h : 0 },
        queue_middle_hover : false,
        queue_pub_play     : $("#queuepubplay"),
        queue_pub_pause    : $("#queuepubpause"),
        queue_interval_sel : $("#pubinterval"),
        txtoverflow_buffer : $("#b_txtoverflow-buffer"),
        no_entry_tutorial  : $("#noentrymsg")
    }; 
    
    function window_update()
    {
        var _w = { height : $(window).height() - 
                            my_template.main_container.position().top,
                   width  : $(window).width() };

        my_template.queue_middle.width(_w.width - parseInt(my_template.queue_middle.css('margin-left')));
        my_template.queue_middle.height(_w.height - my_template.queue_middle.position().top);

        my_template.queue_middle_area.x = my_template.queue_middle.offset().left;
        my_template.queue_middle_area.y = my_template.queue_middle.offset().top;
        my_template.queue_middle_area.w = my_template.queue_middle.width();
        my_template.queue_middle_area.h = my_template.queue_middle.height();
    }

    /* events */

    $(window).resize(function()
    {
        window_update();
    });

    function on_mouse_wheel(e)
    {
        if(my_template.queue_middle_hover==true && $.browser.msie) // Emulate wheel scroll on IE
        {
            var j = null;
            e = e ? e : window.event;
            j = e.detail ? e.detail * -1 : e.wheelDelta / 2;
            my_template.queue_middle.scrollTop(my_template.queue_middle.scrollTop() - j);
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

        my_template.queue_middle_hover = mouse_is_over_area(_mp.x, _mp.y, my_template.queue_middle_area);
    }

    $(window).bind('mousemove', function(e) /* Mozilla */
    {
        on_mouse_move(e);
    }); 

    window.onmousemove = document.onmousemove = on_mouse_move; /* IE */

    /* controls */

    my_template.entry_list.find('div.ety')
        .find('div.etylab')
        .find('a').live('click', function()
    {
        if(active_request==true) { return false; }

        var _pt = $(this).parent().parent();

        if(_pt.hasClass('ety-op'))
        { 
            entry_hide_current();
            $(this).blur();
            return false; 
        }

        entry_hide_current();
        entry_show(_pt.attr('entry'));
        entry_show_fix_vertical();

        $(this).blur();
        return false;
    });

    my_template.entry_list.find('div.ety')
        .find('div.etyedlnk')
        .find('a').live('click', function()
    {
        var _pt = $(this).parent().parent();
        var _st = _pt.attr('status')

        entry_edit(_pt.attr('entry'));

        $(this).blur();
        return false;
    });

    my_template.edit_form
        .find("button[name='editformsave']")
        .live('click', function()
    {
        if(active_request==true) { return false; }
        entry_save_current();
    });

    my_template.edit_form
        .find("button[name='editformcancel']")
        .live('click', function()
    {
        if(active_request==true) { return false; }
        my_template.edit_form.b_modal_close();
    });

    my_template.entry_list.find('div.ety')
        .find('div.etytog')
        .live('click', function()
    {
        entry_delete($(this).parent().attr('entry'));
        return false;
    });

    my_template.queue_header
        .find('button.queuepubbtn')
        .live('click', function()
    {
        <?php if($this->register_confirmation==false) : ?>
        my_template.confirmation_form.b_modal();
        <?php else : ?>
        toggle_queue_publication();
        <?php endif ?>
    });

    my_template.confirmation_send.click(function()
    {
        confirmation_send();
        $(this).blur();
        return false;
    });

    my_template.confirmation_ccel.click(function()
    {
        my_template.confirmation_form.b_modal_close();
        return false;
    });

    $(document).bind('blog_publication_auto_updated', function()
    {
        set_queue_publication();
        set_queue_publication_auto();
    });

    my_template.queue_interval_sel.change(function()
    {
        if((my_queue.interval = $(this).find('option:selected').val()))
        {
            $(this).blur();
            set_queue_publication_auto();
        }
    });

    /* initialize */

    $(document).bind('blog_changed' , function(e)
    {
        on_blog_change();
    });

    $(document).bind('blog_loaded' , function(e)
    {
        on_blog_load();
    });

    blog_load();
    window_update();
    queue_editor_init();
});
