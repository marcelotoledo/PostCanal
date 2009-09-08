var my_template = null;

var my_queue = 
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

var my_updater =
{
    interval : 15000,
    request  : false
}


function entry_set_status(e, s)
{
    if(typeof e == 'string' && e.length > 0)
    {
        e = my_template.entry_list.find("div.ety[entry='" + e + "']");
    }

    if(typeof e == 'object')
    {
        e.attr('status', s);

        //if(s=='waiting')
        //{
        //    e.find('div.etytog').html('<input type="checkbox" checked disabled/>');
        //}
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
            entry                    : $(this).find('entry').text(),
            entry_title              : $(this).find('entry_title').text(),
            entry_content            : $(this).find('entry_content').text(),
            publication_status       : $(this).find('publication_status').text(),
            publication_date         : $(this).find('publication_date').text(),
            publication_date_diff    : $(this).find('publication_date_diff').text(),
            publication_date_literal : $(this).find('publication_date_literal').text(),
            publication_date_local   : $(this).find('publication_date_local').text(),
            ordering                 : $(this).find('ordering').text()
        };

        if(my_queue.data[_data.entry]==undefined) // avoid dupl
        {
            _item  = my_template.entry_blank.clone();
            _inner = _item.find('div.ety');
            _inner.attr('entry', _data.entry);
            _inner.attr('ord', _data.ordering);

            entry_set_status(_inner, _data.publication_status);

            _inner.find('span.etytt').text(_data.entry_title);

            if(parseInt(_data.publication_date_diff) <= 0 &&
                        _data.publication_status != 'published')
            {
                _inner.find('div.etydte').text("<?php echo $this->translation()->overdue ?>");
            }
            else
            {
                _inner.find('div.etydte').text(_i < 5 ? _data.publication_date_literal :
                                                        _data.publication_date_local);
            }

            _lsdata[_i] = _item.html(); _i++;
        }

        my_queue.data[_data.entry] =
        {
            title   : _data.entry_title,
            content : _data.entry_content
        };
    });

    var _pls = my_template.entry_list.find("div.ety[status='published']").eq(0);

    if(_pls.length>0)
    {
        _pls.before(_lsdata.join("\n"));
    }
    else
    {
       my_template.entry_list.append(_lsdata.join("\n"));
    }

    entry_sortable_init();
    my_template.entry_list.scrollTop(0);
}

function entry_list_callback(d)
{
    my_queue.data = Array();
    my_queue.current = null;
    my_template.entry_list.html('');
    entry_populate(d.find('result').find('queue').children());
    entry_populate(d.find('result').find('published').children());
}

function entry_list()
{
    do_request('GET', './queue/list', { blog : my_blog.current }, entry_list_callback);
}

function entry_scroll_top()
{
    if(my_queue.current)
    {
        my_template.entry_list.animate(
        {
            scrollTop: my_queue.current.position().top -
                my_template.entry_list.position().top +
                my_template.entry_list.scrollTop() - 2
        }, 200);
    }
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
    e.removeClass('ety-op').next('div.etyview').remove();
    e.removeClass('ety-op').next('div.etyform').remove();
    $("#my_source_editor").remove();
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
        my_queue.current = my_template.entry_list
            .find("div.ety[entry='" + e + "']");

        var _op = my_queue.current.hasClass('ety-op');
        var _content = _op ?
            _content = my_template.entry_list.find('div.etyview') : 
                       my_template.content_blank.clone();

        _content.find('h1').html(my_queue.data[e].title);
        _content.find('div.etybody').html(my_queue.data[e].content);

        if(my_queue.current.hasClass('ety-op')==false)
        {
            my_queue.current.after(_content.html()).addClass('ety-op');
        }
    }
}

function queue_editor_init()
{
    var _sz = { W : $(window).width()  * 0.75 , 
                H : $(window).height()   -280 };

    my_queue.editor = new FCKeditor("FCKQueueEntryEditor", _sz.W, _sz.H);
    my_queue.editor.Config["CustomConfigurationsPath"] = "../../js/fckconfig.js?t=<?php echo time() ?>";
    my_queue.editor.Config["EditorAreaCSS"] = "../../css/fck_editorarea.css?t=<?php echo time() ?>";
    my_queue.editor.Config["AutoDetectLanguage"] = false;
    my_queue.editor.Config["DefaultLanguage"] = "<?php echo substr($this->session()->getCulture(), 0, 2) ?>";
}

function FCKeditor_OnComplete(i)
{
    i.SetData(my_queue.data[my_queue.current.attr('entry')].content);
    set_active_request(false);
}

function entry_edit(e)
{
    if(my_queue.data[e])
    {
        my_queue.current = my_template.entry_list.find("div.ety[entry='" + e + "']");

        var _rect = { L : $(window).width()  * 0.1 , 
                      T :                       50 ,
                      W : $(window).width()  * 0.8 , 
                      H : $(window).height()  -100 };

        my_template.edit_form
            .css('width',  _rect.W)
            .css('height', _rect.H)
            .modal({ position   : [ _rect.T, _rect.L ], 
                     focus      : true, 
                     opacity    : 75, 
                     autoResize : true });

        my_template.edit_form.find('div.form-bot').css('top', _rect.H - 55); // position hack
        my_template.edit_form.find("input[name='entrytitle']").val(my_queue.data[e].title).focus();
        set_active_request(true);
        my_template.edit_form.find("textarea[name='entrybody']").replaceWith(my_queue.editor.CreateHtml());
    }
}

function entry_save_callback(d)
{
    var _e = d.find('entry').text();
    my_queue.data[_e].title = d.find('title').text();
    my_queue.data[_e].content = d.find('content').text();
    my_queue.current.find('span.etytt').text(my_queue.data[_e].title);
    $.modal.close();
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
        content : FCKeditorAPI.GetInstance("FCKQueueEntryEditor").GetData()
    };

    do_request('POST', './queue/update', _data, entry_save_callback);
}

function entry_delete_callback(d)
{
    my_template.entry_list.find("div.ety[entry='" + d.find('entry').text() + "']").remove();
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

    my_template.entry_list.find('div.ety').each(function()
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
    my_template.entry_list.sortable(
    {
        handle : "div.entrydndhdr",
        items : "div.ety[status!='published']",
        cancel : "div.ety-op",
        distance : 10,
        start: function(e,u)
        {
            entry_hide_current();
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
        my_queue.publication = (my_blog.info['publication_auto']==1);
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

    do_request('POST', './queue/auto', _data, function() { entry_list(); });
}

function toggle_queue_enqueueing()
{
    my_queue.enqueueing = my_queue.enqueueing ^ true;
    blog_update('enqueueing_auto', (my_queue.enqueueing ? 1 : 0));
}

function set_queue_enqueueing()
{
    if(my_queue.enqueueing==null)
    {
        my_queue.enqueueing = (my_blog.info['enqueueing_auto']==1);
    }

    if(my_queue.enqueueing)
    {
        my_template.enqueue_no.hide();
        my_template.enqueue_yes.show();
    }
    else
    {
        my_template.enqueue_yes.hide();
        my_template.enqueue_no.show();
    }
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
    if(my_updater.request==true) { return false; }

    var _wdom = my_template.entry_list.find("div.ety[status='waiting']");
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
    if(my_queue.enqueueing!=true || my_updater.request==true) { return false; }
    _data = { blog : my_blog.current };
    do_request('GET', './queue/list', _data, enqueue_updater_callback);
}

function updater_run()
{
    var _i = my_updater.interval / 3;
    setTimeout('publication_updater()', _i * 1);
    setTimeout('enqueue_updater()', _i * 2);
    updater_init();
}

function updater_init()
{
    setTimeout('updater_run()', my_updater.interval);
}

function initialize()
{
    my_queue.data        = Array();
    my_queue.current     = null;
    my_queue.sorting     = false;
    my_queue.publication = null;
    my_queue.interval    = 0;
    my_queue.enqueueing  = null;

    set_queue_publication();
    // set_queue_enqueueing();
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
    my_template =
    {
        main_container     : $("#mainct"),
        // queue_container    : $("#queuecontainer"),
        queue_header       : $("#tplbar"),
        queue_middle       : $("#etylst"),
        entry_list         : $("#etylst"),
        entry_blank        : $("#entryblank"),
        content_blank      : $("#contentblank"),
        edit_form          : $("#editform"),
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
                            my_template.main_container.position().top,
                   width  : $(window).width() };

        // my_template.queue_container.width(_w.width);
        // my_template.queue_container.height(_w.height);

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
        $.modal.close();
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
        toggle_queue_publication();
    });

    $(document).bind('blog_publication_auto_updated', function()
    {
        set_queue_publication();
        set_queue_publication_auto();
    });

    my_template.queue_header
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
