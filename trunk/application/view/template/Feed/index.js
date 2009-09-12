var my_template = null;

function feed_add_show()
{
    my_template.feed_type_failed.hide();
    my_template.new_feed_form.show();
    my_template.new_feed_url.val('');
    my_template.new_feed_url.focus();

    if($("#nofeedmsg0").is(':visible')) /* tutorial */
    {
        $("#nofeedmsg0").hide(100);
        $("#nofeedmsg1").show(100);
    }
}

function feed_add_hide()
{
    my_template.new_feed_form.hide();
    my_template.feed_type_failed.hide();
    my_template.feed_options_form.hide();
}

function feed_add_toggle()
{
    (my_template.new_feed_form.css('display')=='block') ?
        feed_add_hide() :
        feed_add_show() ;
}

function add_message(m)
{
    my_template.new_feed_message.text(m);
    (m=='') ? my_template.new_feed_message.hide() : my_template.new_feed_message.show();
}

function txtoverflow_up()
{
    my_template.feed_list_area.find('div.feed').each(function()
    {
        $(this).find('div.feedtit').b_txtoverflow({ buffer: my_template.txtoverflow_buffer, width: (my_template.feed_list_area.width() * 0.8) });
    });
}

function feed_populate(b, app)
{
    if(b==undefined) { return false; }

    var _feed = 
    {
        feed    : b.find('feed').text(),
        ord     : b.find('ordering').text(),
        url     : b.find('feed_url').text(),
        title   : b.find('feed_title').text(),
        enabled : b.find('enabled').text()
    };

    if(my_template.feed_list_ref[_feed.feed]!=undefined) // avoid dupl
    {
        return false;
    }

    var _item = my_template.feed_item_blank.clone();

    _item.find('div.feed')
        .attr('feed', _feed.feed)
        .attr('ord', _feed.ord);

    (app==true) ? my_template.feed_list_area.append(_item.html()) : 
                  my_template.feed_list_area.prepend(_item.html()) ; 

    my_template.feed_list_ref[_feed.feed] = 
    {
        item : my_template.feed_list_area.find("div.feed[feed='" + _feed.feed + "']")
    };
    
    my_template.feed_list_ref[_feed.feed].item.find('div.feedtit').b_txtoverflow({ buffer: my_template.txtoverflow_buffer, width: (my_template.feed_list_area.width()), text: _feed.title });
    my_template.feed_list_ref[_feed.feed].item.find('div.feedurl > span').text(_feed.url);
    my_template.feed_list_ref[_feed.feed].item.find("input[name='title']").val(_feed.title);
}

function feed_options(r)
{
    var _option = null;
    var _url    = '';
    var _title  = '';
    var _output = Array();
    var _i = 0;

    r.each(function()
    {
        _url = $(this).find('feed_url').text();
        _title = $(this).find('feed_title').text();
        _option = my_template.feed_option_blank.clone();
        _option.find(my_template.feed_option_selector)
            .attr('url', _url)
            .after(' ' + ((_title.length>0) ? _title : _url));
        _output[_i] = _option.html(); _i++;
    });

    my_template.feed_options_form.find('div.inputfeedoption').remove();
    my_template.feed_options_form.find('form').prepend(_output.join(''));
    my_template.feed_options_form.find(my_template.feed_option_selector + ":first").attr('checked', true);

    feed_add_hide();
    my_template.feed_options_form.show();
}

function feed_discover_callback(d)
{
    if(d.find('overquota').text()=="true")
    {
        add_message("<?php echo $this->translation()->overquota ?>");
        return false;
    }

    var _results = d.find('results').find('item');

    if(_results.length == 0)
    {
        add_message("<?php echo $this->translation()->feed_not_found ?>");
        return false;
    }

    if(_results.length > 1)
    {
        feed_options(_results);
        return false;
    }

    var _status = _results.find('feed_status').text();
        
    if(_status=="200")
    {
        feed_add(_results.find('feed_url').text());
        feed_add_hide();
    }
    else
    {
        add_message("<?php echo $this->translation()->feed_error ?>");
    }
}

function feed_discover()
{
    var _data = { url: my_template.new_feed_url.val() };

    if(_data.url=="")
    {
        add_message("<?php echo $this->translation()->form_incomplete ?>");
        return false;
    }

    if(_data.url.indexOf('://')==-1)
    {
        _data.url = 'http://' + _data.url;
        my_template.new_feed_url.val(_data.url);
    }

    add_message(''); 
    do_request('POST', './feed/discover', _data, feed_discover_callback);
}

function feed_add_callback(d)
{
    feed_populate(d.find('feed'), false);

    if($("#nofeedmsg1").is(':visible')) /* tutorial */
    {
        $("#nofeedmsg1").hide(100);
        $("#nofeedmsg2").show(100);
    }
}

function feed_add(u)
{
    var _data = { url : u, blog: my_blog.current };
    add_message(''); 
    do_request('POST', './feed/add', _data, feed_add_callback);
}

function feed_list_callback(d)
{
    my_template.feed_list_area.html(''); 
    my_template.feed_list_ref = Array();

    var _fl = d.find('feeds').children();

    if(_fl.length==0)
    {
        $("#nofeedmsg0").show(100); // tutorial
    }

    d.find('feeds').children().each(function()
    {
        feed_populate($(this), true);
    })
}

function feed_list()
{
    $.ajax
    ({
        type: "GET",
        url: "./feed/list",
        dataType: "xml",
        data: { blog: my_blog.current },
        beforeSend: function () { set_active_request(true); },
        complete: function ()   { set_active_request(false); 
                                  $(document).trigger('after_feed_list'); },
        success: function (xml) { feed_list_callback($(xml).find('data')); },
        error: function () { server_error(); }
    });
}

function feed_edit_show(b)
{
    my_template.feed_list_ref[b].item.find('button.feededitbtn').hide();
    my_template.feed_list_ref[b].item.find('button.feeddeletebtn').show();
    disable_submit(); // form have only one input, disable submit from this
    my_template.feed_list_ref[b].item.find('div.feedbot').show();
}

function feed_edit_hide(b)
{
    my_template.feed_list_ref[b].item.find('div.feedbot').hide();
    my_template.feed_list_ref[b].item.find('button.feeddeletebtn').hide();
    my_template.feed_list_ref[b].item.find('button.feededitbtn').show();
}

function feed_remove_from_list(b)
{
    //my_template.feed_list_ref[b].item.next('div.feeddeletemsg').remove();
    my_template.feed_list_ref[b].item.remove();
    my_template.feed_list_ref[b] = null;
    flash_message("<?php echo $this->translation()->deleted ?>");
}

function feed_delete_confirm_show(b)
{
    //feed_edit_hide(b);
    my_template.feed_list_ref[b].item.find('button.feededitbtn').hide();
    my_template.feed_list_ref[b].item.find('button.feeddeletebtn').hide();
    var _form = my_template.feed_list_ref[b].item.find('div.feedbot > form');
    _form.hide();
    _form.after(my_template.feed_delete_blank.clone().html());
}

function feed_delete_confirm_hide(b)
{
    var _form = my_template.feed_list_ref[b].item.find('div.feedbot > form');
    _form.next('div.feeddeletemsg').remove();
    feed_edit_hide(b);
    _form.show();
    // my_template.feed_list_ref[b].item.next('div.feeddeletemsg').remove();
    my_template.feed_list_ref[b].item.find('button.feededitbtn').show();
}

function feed_delete_callback(d)
{
    feed_remove_from_list(d.find('result').text());
}

function feed_delete(f)
{
    var _data = { blog: my_blog.current, feed: f };
    do_request('POST', './feed/delete', _data, feed_delete_callback);
}

function feed_update_callback(d)
{
    var _updated = null;

    if((_updated = d.find('updated'))!=undefined)
    {
        var _feed = _updated.find('feed').text();
        var _title = _updated.find('feed_title').text();
        my_template.feed_list_ref[_feed].item.find('div.feedtit').b_txtoverflow({ buffer: my_template.txtoverflow_buffer, width: (my_template.feed_list_area.width() * 0.8), text: _title });
        flash_message("<?php echo $this->translation()->saved ?>");
        feed_edit_hide(_feed);
    }
}

function feed_update(f)
{
    var _up = 
    {
        feed       : f,
        blog       : my_blog.current,
        feed_title : my_template.feed_list_ref[f].item.find("input[name='title']").val()
    }

    do_request('POST', './feed/update', _up, feed_update_callback);
}

function feed_position_callback(d)
{
    if((d.find('updated').text()=="true")!=true)
    {
        feed_list();
    }
}

function feed_position(f, p)
{
    var _data = { blog : my_blog.current, feed : f, position : p };
    do_request('POST', './feed/position', _data, feed_position_callback);
}

function sortable_callback(feed)
{
    var _p = 1;

    my_template.feed_list_area.find('div.feed').each(function()
    {
        if(feed == $(this).attr('feed') && _p != $(this).attr('ord'))
        {
            feed_position(feed, _p);
        }

        _p++;
    });
}

function feed_sortable_init()
{
    my_template.feed_list_area.sortable(
    { 
        stop: function(e, ui)
        {
            sortable_callback(ui.item.attr('feed'));
        },
        handle: "div.feeddndhdr",
        distance: 10
    });
    my_template.feed_list_area.disableSelection();
}

function on_blog_change()
{
    feed_add_hide();
    feed_list(); 
}

$(document).ready(function()
{
    my_template =
    {
        new_feed_button      : $("#addnewfeedbtn"),
        new_feed_form        : $("#addnewfeedform"),
        feed_options_form    : $("#feedoptionsform"),
        feed_options_submit  : $("#optsubmit"),
        feed_options_message : $("#optmessage"),
        feed_option_blank    : $("#feedoptionblank"),
        feed_option_selector : "input[name='inputfeedoption']",
        new_feed_url         : $("#feedurl"),
        new_feed_submit      : $("#addsubmit"),
        new_feed_message     : $("#addmessage"),
        feed_list_area       : $("#feedlistarea"),
        feed_item_blank      : $("#feeditemblank"),
        feed_delete_blank    : $("#feeddeleteblank"),
        feed_type_failed     : $("#feedtypefailedmsg"),
        txtoverflow_buffer   : $("#b_txtoverflow-buffer"),
        feed_list_ref        : Array()
    }; 

    function window_update()
    {
        txtoverflow_up();
    }

    $(window).resize(function()
    {
        window_update();
    });

    function no_feed()
    {
        $("#nofeedmsg0").show(); /* tutorial */
    }

    function initialize()
    {
        feed_list();
    }
    
    my_template.new_feed_button.click(function()
    {
        if(active_request==false)
        {
           if(my_template.feed_options_form.is(':visible')) { feed_add_hide();   }
           else                                             { feed_add_toggle(); }
        }
    });

    my_template.new_feed_url.keypress(function(e)
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            if(active_request==false) { feed_discover(); }
        }
    });

    my_template.new_feed_submit.click(function()
    {
        if(active_request==false) { feed_discover(); }
        $(this).blur();
        return false;
    });

    function feed_item_getid(i)
    {
        return i.parent().parent().attr('feed')
    }

    my_template.feed_list_area.find('button.feededitbtn').live('click', function()
    {
        feed_edit_show(feed_item_getid($(this)));
        return false;
    });

    my_template.feed_list_area.find('button.feeddeletebtn').live('click', function()
    {
        feed_delete_confirm_show(feed_item_getid($(this)));
        return false;
    });

    function feed_update_getid(i)
    {
        return i.parent().parent().parent().parent().attr('feed')
    }

    my_template.feed_list_area.find("button.feedupdatebtn").live('click', function()
    {
        feed_update(feed_update_getid($(this)));
        return false;
    });

    my_template.feed_list_area.find("button.feedcancelbtn").live('click', function()
    {
        feed_edit_hide(feed_update_getid($(this)));
        return false;
    });

    function feed_delete_getid(i)
    {
        return i.parent().parent().parent().parent().parent().attr('feed');
    }

    my_template.feed_list_area.find("button[name='feeddeletebtn']").live('click', function()
    {
        feed_delete(feed_delete_getid($(this)));
        return false;
    });

    my_template.feed_list_area.find("button[name='feednodelbtn']").live('click', function()
    {
        feed_delete_confirm_hide(feed_delete_getid($(this)));
        return false;
    });

    my_template.feed_options_submit.live('click', function()
    {
        my_template.new_feed_url.val(my_template.feed_options_form
            .find(my_template.feed_option_selector + ":checked").attr('url'));
        feed_discover();
    });

    $(document).bind('blog_changed' , function(e)
    {
        on_blog_change();
    });

    $(document).bind('after_feed_list' , function(e)
    {
        feed_sortable_init();
    });

    initialize();
});
