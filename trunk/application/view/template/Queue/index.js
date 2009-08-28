var mytpl = null;

var queue = 
{
    data        : Array() ,
    current     : null    ,
    editor      : null    ,
    sorting     : false   ,
    publication : null    ,
    interval    : 0       ,
    enqueueing  : null    ,
    request     : false
};

var updater =
{
    interval : 15000,
    request  : false
}

var magic_fck_position = { l: 150, t: 280, h: 750, v: 120 };


function entry_set_status(e, s)
{
    if(typeof e == 'string' && e.length > 0)
    {
        e = mytpl.entry_list.find("div.ety[entry='" + e + "']");
    }

    if(typeof e == 'object')
    {
        e.attr('status', s);

        if(s=='waiting')
        {
            e.find('div.etytog').html('<input type="checkbox" checked disabled/>');
        }
        if(s=='failed')
        {
            e.find('div.etytog')
                .html('<nobr><input type="checkbox"/><img src="/image/warning.png"/></nobr>');
        }
        if(s=='published')
        {
            e.find('div.etytog').html("<b>(P)</b>");
        }
    }
}

function entry_populate(d)
{
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
            entry                  : $(this).find('entry').text(),
            entry_title            : $(this).find('entry_title').text(),
            entry_content          : $(this).find('entry_content').text(),
            publication_status     : $(this).find('publication_status').text(),
            publication_date       : $(this).find('publication_date').text(),
            publication_date_local : $(this).find('publication_date_local').text(),
            ordering               : $(this).find('ordering').text()
        };

        if(queue.data[_data.entry]==undefined) // avoid dupl
        {
            _item  = mytpl.entry_blank.clone();
            _inner = _item.find('div.ety');
            _inner.attr('entry', _data.entry);
            _inner.attr('ord', _data.ordering);

            entry_set_status(_inner, _data.publication_status);

            _inner.find('span.etytt').text(_data.entry_title);
            _inner.find('div.etydte').text(_data.publication_date_local);

            _lsdata[_i] = _item.html(); _i++;
        }

        queue.data[_data.entry] =
        {
            title   : _data.entry_title,
            content : _data.entry_content
        };
    });

    var _pls = mytpl.entry_list.find("div.ety[status='published']").eq(0);

    if(_pls.length>0)
    {
        _pls.before(_lsdata.join("\n"));
    }
    else
    {
        mytpl.entry_list.append(_lsdata.join("\n"));
    }

    entry_sortable_init();
    mytpl.entry_list.scrollTop(0);
}

function entry_list_callback(d)
{
    queue.data = Array();
    queue.current = null;
    mytpl.entry_list.html('');
    entry_populate(d.find('result').find('queue').children());
    entry_populate(d.find('result').find('published').children());
}

function entry_list()
{
    do_request('GET', './queue/list', { blog : my_blog.current }, entry_list_callback);
}

function entry_scroll_top()
{
    if(queue.current)
    {
        mytpl.entry_list.animate(
        {
            scrollTop: queue.current.position().top -
                mytpl.entry_list.position().top +
                mytpl.entry_list.scrollTop() - 2
        }, 200);
    }
}

function entry_show_fix_vertical()
{
    var _rmh = mytpl.queue_middle_area.h / 2;
    var _rmt = mytpl.queue_middle_area.y;
    var _apt = queue.current.position().top;
    var _coh = queue.current.next('div.etyview').outerHeight();

    var _scr = mytpl.entry_list.scrollTop() + 
               _apt -
               ((_coh > _rmh) ? _rmt : _rmh);

    mytpl.entry_list.animate({ scrollTop: _scr }, 200);
}

function entry_hide(e)
{
    e.removeClass('ety-op').next('div.etyview').remove();
    e.removeClass('ety-op').next('div.etyform').remove();
    $("#my_source_editor").remove();
}

function entry_hide_current()
{
    if(queue.current) { entry_hide(queue.current); }
    queue.current = null;
}

function entry_show(e)
{
    if(queue.data[e])
    {
        var _content = mytpl.content_blank.clone();

        _content.find('div.etyviewtitle').html(queue.data[e].title);
        _content.find('div.etyviewbody').html(queue.data[e].content);

        queue.current = mytpl.entry_list.find("div.ety[entry='" + e + "']");
        queue.current.after(_content.html()).addClass('ety-op');
    }
}

function queue_editor_init()
{
    var _size = { w : $(window).width() - magic_fck_position.l ,
                  h : $(window).height() - magic_fck_position.t };

    if(_size.w < magic_fck_position.h) { _size.w = magic_fck_position.h; }
    if(_size.h < magic_fck_position.v) { _size.h = magic_fck_position.v; }
    
    queue.editor = new FCKeditor("FCKQueueEntryEditor", _size.w, _size.h);
    queue.editor.Config["CustomConfigurationsPath"] = "../../js/fckconfig.js?t=<?php echo time() ?>";
    queue.editor.Config["EditorAreaCSS"] = "../../css/fck_editorarea.css?t=<?php echo time() ?>";
    queue.editor.Config["AutoDetectLanguage"] = false;
    queue.editor.Config["DefaultLanguage"] = "<?php echo substr($this->session()->getCulture(), 0, 2) ?>";
}

function FCKeditor_OnComplete(i)
{
    i.SetData(queue.data[queue.current.attr('entry')].content);
    set_active_request(false);
}

function entry_edit(e)
{
    if(queue.data[e])
    {
        var _form = mytpl.edit_form_blank.clone();

        queue.current = mytpl.entry_list.find("div.ety[entry='" + e + "']");
        queue.current.after(_form.html()).addClass('ety-op');

        _form = queue.current.next('div.etyform');
        _form.find("input[name='entrytitle']").val(queue.data[e].title).focus();
        set_active_request(true);
        _form.find("textarea[name='entrybody']").replaceWith(queue.editor.CreateHtml());
    }
}

function entry_save_callback(d)
{
    var _e = d.find('entry').text();
    queue.data[_e].title = d.find('title').text();
    queue.data[_e].content = d.find('content').text();
    queue.current.find('span.etytt > a').text(queue.data[_e].title);
    entry_hide_current();
    flash_message("<?php echo $this->translation()->saved ?>");
}

function entry_save_current()
{
    var _data = 
    { 
        blog    : my_blog.current , 
        entry   : queue.current.attr('entry'),
        title   : queue.current.next('div.etyform').find("input[name='entrytitle']").val(),
        content : FCKeditorAPI.GetInstance("FCKQueueEntryEditor").GetData()
    };

    do_request('POST', './queue/update', _data, entry_save_callback);
}

function entry_delete_callback(d)
{
    mytpl.entry_list.find("div.ety[entry='" + d.find('entry').text() + "']").remove();
}

function entry_delete(e)
{
    var _data = { blog  : my_blog.current , entry : e };
    entry_hide_current();
    do_request('POST', './queue/delete', _data, entry_delete_callback);
}

function entry_position_callback(d)
{
    // if((d.find('updated').text()=='true')!=true) { entry_list(); }
    entry_list();
}

function entry_position(e, p)
{
    var _data = { blog  : my_blog.current , entry : e, position: p };
    do_request('POST', './queue/position', _data, entry_position_callback);
}

function entry_sortable_callback(e)
{
    var _p = 1;

    mytpl.entry_list.find('div.ety').each(function()
    {
        if(e==$(this).attr('entry') && _p != $(this).attr('ord'))
        {
            entry_position(e, _p);
        }

        _p++;
    });
}

function entry_sortable_init()
{
    mytpl.entry_list.sortable(
    {
        handle : "div.entrydndhdr",
        items : "div.ety[status!='published']",
        cancel : "div.ety-op",
        distance : 10,
        start: function(e,u)
        {
            entry_hide_current();
            queue.sorting = true;
        },
        update: function (e,u)
        {
            entry_sortable_callback(u.item.attr('entry'));
        }
    });
    mytpl.entry_list.disableSelection();
}

function toggle_queue_publication()
{
    queue.publication = queue.publication ^ true;
    blog_update('publication_auto', (queue.publication ? 1 : 0));
}

function set_queue_publication()
{
    if(queue.publication==null)
    {
        queue.publication = (my_blog.info['publication_auto']==1);
    }

    if(queue.publication)
    {
        mytpl.queue_pub_play.hide();
        mytpl.queue_pub_pause.show();
    }
    else
    {
        mytpl.queue_pub_pause.hide();
        mytpl.queue_pub_play.show();
    }
}

function set_queue_publication_auto()
{
    var _data = { blog        : my_blog.current   ,
                  interval    : queue.interval ,
                  publication : (queue.publication ? 1 : 0) };

    do_request('POST', './queue/auto', _data, function() { entry_list(); });
}

function toggle_queue_enqueueing()
{
    queue.enqueueing = queue.enqueueing ^ true;
    blog_update('enqueueing_auto', (queue.enqueueing ? 1 : 0));
}

function set_queue_enqueueing()
{
    if(queue.enqueueing==null)
    {
        queue.enqueueing = (my_blog.info['enqueueing_auto']==1);
    }

    if(queue.enqueueing)
    {
        mytpl.enqueue_no.hide();
        mytpl.enqueue_yes.show();
    }
    else
    {
        mytpl.enqueue_yes.hide();
        mytpl.enqueue_no.show();
    }
}

function set_queue_interval()
{
    queue.interval = parseInt(my_blog.info['publication_interval']);

    mytpl.queue_interval_sel.find('option').each(function()
    {
        if($(this).val()==queue.interval)
        {
            $(this).attr('selected', true);
        }
    });
}

function publication_updater_callback(d)
{
    d.find('result').children().each(function()
    {
        entry_set_status($(this).find('entry').text(), 
                         $(this).find('status').text());
    });
}

function publication_updater()
{
    if(updater.request==true) { return false; }

    var _wdom = mytpl.entry_list.find("div.ety[status='waiting']");
    var _wpar = Array();
    var _data = null;

    if(_wdom.length>0)
    {
        _wdom.each(function() { _wpar.push($(this).attr('entry')); });
        _data = { blog : my_blog.current, waiting : _wpar.join(',') };
        do_request('GET', './queue/check', _data, publication_updater_callback);
    }
}

function enqueue_updater_callback(d)
{
    entry_populate(d.find('result').find('queue').children());
}

function enqueue_updater()
{
    if(queue.enqueueing!=true || updater.request==true) { return false; }
    _data = { blog : my_blog.current };
    do_request('GET', './queue/list', _data, enqueue_updater_callback);
}

function updater_run()
{
    var _i = updater.interval / 3;
    setTimeout('publication_updater()', _i * 1);
    setTimeout('enqueue_updater()', _i * 2);
    updater_init();
}

function updater_init()
{
    setTimeout('updater_run()', updater.interval);
}

function initialize()
{
    queue.data        = Array();
    queue.current     = null;
    queue.sorting     = false;
    queue.publication = null;
    queue.interval    = 0;
    queue.enqueueing  = null;

    set_queue_publication();
    set_queue_enqueueing();
    set_queue_interval();

    entry_list();
    updater_init();
}

function on_blog_change()
{
    blog_load();
}

function on_blog_load()
{
    initialize();
}

$(document).ready(function()
{
    mytpl =
    {
        main_container     : $("#mainct"),
        // queue_container    : $("#queuecontainer"),
        queue_header       : $("#tplbar"),
        queue_middle       : $("#etylst"),
        entry_list         : $("#etylst"),
        entry_blank        : $("#entryblank"),
        content_blank      : $("#contentblank"),
        edit_form_blank    : $("#editformblank"),
        queue_middle_area  : { x : 0 , y : 0 , w : 0 , h : 0 },
        queue_middle_hover : false,
        queue_pub_play     : $("#queuepubplay"),
        queue_pub_pause    : $("#queuepubpause"),
        enqueue_yes        : $("#enqueuelnkyes"),
        enqueue_no         : $("#enqueuelnkno"),
        queue_interval_sel : $("#pubinterval")
    }; 
    
    function window_update()
    {
        var _w = { height : $(window).height() - 
                            mytpl.main_container.position().top,
                   width  : $(window).width() };

        // mytpl.queue_container.width(_w.width);
        // mytpl.queue_container.height(_w.height);
        mytpl.queue_middle.height(_w.height - mytpl.queue_middle.position().top - 3);

        mytpl.queue_middle_area.x = mytpl.queue_middle.offset().left;
        mytpl.queue_middle_area.y = mytpl.queue_middle.offset().top;
        mytpl.queue_middle_area.w = mytpl.queue_middle.width();
        mytpl.queue_middle_area.h = mytpl.queue_middle.height();
    }

    /* events */

    $(window).resize(function()
    {
        window_update();
    });

    function on_mouse_wheel(e)
    {
        if(mytpl.queue_middle_hover==true && $.browser.msie) // Emulate wheel scroll on IE
        {
            var j = null;
            e = e ? e : window.event;
            j = e.detail ? e.detail * -1 : e.wheelDelta / 2;
            mytpl.queue_middle.scrollTop(mytpl.queue_middle.scrollTop() - j);
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

        mytpl.queue_middle_hover = mouse_is_over_area(_mp.x, _mp.y, mytpl.queue_middle_area);
    }

    $(window).bind('mousemove', function(e) /* Mozilla */
    {
        on_mouse_move(e);
    }); 

    window.onmousemove = document.onmousemove = on_mouse_move; /* IE */

    /* controls */

    mytpl.entry_list.find('div.ety')
        .find('div.etylab')
        .find('a').live('click', function()
    {
        if(active_request==true) { return false; }

        var _pt = $(this).parent().parent().parent();
        var _st = _pt.attr('status')

        if(_pt.hasClass('ety-op'))
        { 
            entry_hide_current();
            $(this).blur();
            return false; 
        }

        if(_st=='waiting' || _st=='published')
        {
            entry_hide_current();
            entry_show(_pt.attr('entry'));
            entry_show_fix_vertical();
        }
        else
        {
            entry_hide_current();
            entry_edit(_pt.attr('entry'));
            entry_scroll_top();
        }

        $(this).blur();
        return false;
    });

    mytpl.entry_list.find('div.etyform')
        .find("input[name='editformsave']")
        .live('click', function()
    {
        entry_save_current();
    });

    mytpl.entry_list.find('div.etyform')
        .find("input[name='editformcancel']")
        .live('click', function()
    {
        entry_hide_current();
    });

    mytpl.entry_list.find('div.ety')
        .find('div.etytog')
        .live('click', function()
    {
        entry_delete($(this).parent().parent().attr('entry'));
        return false;
    });

    mytpl.queue_header
        .find('button.queuepubbtn')
        .live('click', function()
    {
        toggle_queue_publication();
    });

    $(document).bind('blog_publication_auto_updated', function()
    {
        set_queue_publication();
        set_queue_publication_auto();
    });

    mytpl.queue_header
        .find('div.enqueuelnk')
        .find('a')
        .live('click', function()
    {
        toggle_queue_enqueueing();
        return false;
    });

    $(document).bind('blog_enqueueing_auto_updated', function()
    {
        set_queue_enqueueing();
    });

    mytpl.queue_interval_sel.change(function()
    {
        if((queue.interval = $(this).find('option:selected').val()))
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
