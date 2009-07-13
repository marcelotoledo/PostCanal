var mytpl = null;

var entry = 
{
    data    : Array(),
    current : null
};


function entry_set_status(e, s)
{
    if(typeof e == 'string' && e.length > 0)
    {
        e = mytpl.entry_list.find("div.entry[entry='" + e + "']");
    }

    if(typeof e == 'object')
    {
        e.attr('status', s);

        if(s=='waiting')
        {
            e.find('div.entrybutton').html('<input type="checkbox" checked disabled/>');
        }
        if(s=='failed')
        {
            e.find('div.entrybutton')
                .html('<nobr><input type="checkbox"/><img src="/image/warning.png"/></nobr>');
        }
        if(s=='published')
        {
            e.find('div.entrybutton').html("<b>(P)</b>");
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

        if(entry.data[_data.entry]==undefined) // avoid dupl
        {
            _item  = mytpl.entry_blank.clone();
            _inner = _item.find('div.entry');
            _inner.attr('entry', _data.entry);
            _inner.attr('ord', _data.ordering);

            entry_set_status(_inner, _data.publication_status);

            _inner.find('div.entrytitle > a').text(_data.entry_title);

            if(_data.publication_status=='waiting' ||
               _data.publication_status=='published')
            {
                _inner.find('div.entrydate').text(_data.publication_date_local);
            }

            _lsdata[_i] = _item.html(); _i++;
        }

        entry.data[_data.entry] =
        {
            title   : _data.entry_title,
            content : _data.entry_content
        };
    });

    var _pls = mytpl.entry_list.find("div.entry[status='published']").eq(0);

    if(_pls.length>0)
    {
        _pls.before(_lsdata.join("\n"));
    }
    else
    {
        mytpl.entry_list.append(_lsdata.join("\n"));
    }

    mytpl.entry_list.scrollTop(0);
}

function entry_list_callback(d)
{
    entry.data = Array();
    entry.current = null;
    mytpl.entry_list.html('');
    entry_populate(d.find('result').find('queue').children());
    entry_populate(d.find('result').find('published').children());
}

function entry_list()
{
    do_request('GET', './queue/list', { blog : blog.current }, entry_list_callback);
}

function entry_scroll_top()
{
    if(entry.current)
    {
        mytpl.entry_list.scrollTop(
            entry.current.position().top -
            mytpl.entry_list.position().top +
            mytpl.entry_list.scrollTop() - 2
        );
    }
}

function entry_show_fix_vertical()
{
    var _rmh = mytpl.queue_middle_area.h / 2;
    var _rmt = mytpl.queue_middle_area.y;
    var _apt = entry.current.position().top;
    var _coh = entry.current.next('div.content').outerHeight();

    var _scr = mytpl.entry_list.scrollTop() + 
               _apt -
               ((_coh > _rmh) ? _rmt : _rmh);

    mytpl.entry_list.scrollTop(_scr);
}

function entry_hide(e)
{
    e.removeClass('entryopen').next('div.content').remove();
    e.removeClass('entryopen').next('div.editform').remove();
}

function entry_hide_current()
{
    if(entry.current) { entry_hide(entry.current); }
    entry.current = null;
}

function entry_show(e)
{
    if(entry.data[e])
    {
        var _content = mytpl.content_blank.clone();

        _content.find('div.contenttitle').html(entry.data[e].title);
        _content.find('div.contentbody').html(entry.data[e].content);

        entry.current = mytpl.entry_list.find("div.entry[entry='" + e + "']");
        entry.current.after(_content.html()).addClass('entryopen');
    }
}

function entry_edit(e)
{
    if(entry.data[e])
    {
        var _form = mytpl.edit_form_blank.clone();

        _form.find("div.editform")
             .find("input[name='entrytitle']").val(entry.data[e].title);

        /*
        _form.find('div.editformtitle').html(entry.data[e].title);
        _form.find('div.editformbody').html(entry.data[e].content);
        */

        entry.current = mytpl.entry_list.find("div.entry[entry='" + e + "']");
        entry.current.after(_form.html()).addClass('entryopen');
    }
}

function entry_publish(e)
{
    alert(e);
}

function on_blog_change()
{
    entry_list();
}

$(document).ready(function()
{
    mytpl =
    {
        main_container     : $("#maincontainer"),
        queue_container    : $("#queuecontainer"),
        queue_header_title : $("#queueheadertitle"),
        queue_middle       : $("#queuemiddle"),
        entry_list         : $("#queuemiddle"),
        entry_blank        : $("#entryblank"),
        content_blank      : $("#contentblank"),
        edit_form_blank    : $("#editformblank"),
        queue_middle_area  : { x : 0 , y : 0 , w : 0 , h : 0 },
        queue_middle_hover : false,
        queue_footer       : $("#queuefooter"),
        entry_expanded_lnk : $("#entryexpandedlnk"),
        entry_expanded_lab : $("#entryexpandedlab"),
        entry_list_lnk     : $("#entrylistlnk"),
        entry_list_lab     : $("#entrylistlab"),
        entry_next         : $("#entrynext"),
        entry_prev         : $("#entryprev")
    }; 
    
    function window_update()
    {
        var _w = { height : $(window).height() - 
                            mytpl.main_container.position().top,
                   width  : $(window).width() };

        mytpl.queue_container.width(_w.width);
        mytpl.queue_container.height(_w.height);
        mytpl.queue_middle.height(_w.height - mytpl.queue_middle.position().top - 3);

        mytpl.queue_middle_area.x = mytpl.queue_middle.offset().left;
        mytpl.queue_middle_area.y = mytpl.queue_middle.offset().top;
        mytpl.queue_middle_area.w = mytpl.queue_middle.width();
        mytpl.queue_middle_area.h = mytpl.queue_middle.height();
    }

    function initialize()
    {
        entry_list();
        window_update();
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

    mytpl.entry_list.find('div.entry')
        .find('div.entryhead')
        .find('div.entrytitle')
        .find('a').live('click', function()
    {
        var _st = $(this).parent().parent().parent().attr('status')

        if(_st=='waiting' || _st=='published')
        {
            entry_hide_current();
            entry_show($(this).parent().parent().parent().attr('entry'));
            entry_show_fix_vertical();
        }
        else
        {
            entry_hide_current();
            entry_edit($(this).parent().parent().parent().attr('entry'));
            entry_scroll_top();
        }

        $(this).blur();
        return false;
    });

    mytpl.entry_list.find('div.entry')
        .find('div.entrybutton')
        .find('input').live('change', function()
    {
        entry_publish($(this).parent().parent().attr('entry'));
        return false;
    });

    /* initialize */

    $(document).bind('blog_changed' , function(e)
    {
        on_blog_change();
    });

    initialize();
});
