var mytpl = null;

function feed_add_show()
{
    mytpl.feed_type_failed.hide();
    mytpl.new_feed_form.show();
    mytpl.new_feed_url.val('');
    mytpl.new_feed_url.focus();
}

function feed_add_hide()
{
    mytpl.new_feed_form.hide();
    mytpl.feed_type_failed.hide();
    mytpl.feed_options_form.hide();
}

function feed_add_toggle()
{
    (mytpl.new_feed_form.css('display')=='block') ?
        feed_add_hide() :
        feed_add_show() ;
}

function add_message(m)
{
    mytpl.new_feed_message.text(m);
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

    if(mytpl.feed_list_ref[_feed.feed]!=undefined) // avoid dupl
    {
        return false;
    }

    var _item = mytpl.feed_item_blank.clone();

    _item.find('div.feeditem')
        .attr('feed', _feed.feed)
        .attr('ord', _feed.ord);
    _item.find('div.feeditemeditform').attr('feed', _feed.feed);

    (app==true) ? mytpl.feed_list_area.append(_item.html()) : 
                  mytpl.feed_list_area.prepend(_item.html()) ; 

    mytpl.feed_list_ref[_feed.feed] = 
    {
        item : mytpl.feed_list_area.find("div.feeditem[feed='" + _feed.feed + "']"),
        form : mytpl.feed_list_area.find("div.feeditemeditform[feed='" + _feed.feed + "']")
    };

    mytpl.feed_list_ref[_feed.feed].item.find('span.feeditemname').text(_feed.title);
    mytpl.feed_list_ref[_feed.feed].item.find('span.feeditemurl').text(_feed.url);
    mytpl.feed_list_ref[_feed.feed].form.find("input[name='title']").val(_feed.title);
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
        _option = mytpl.feed_option_blank.clone();
        _option.find(mytpl.feed_option_selector)
            .attr('url', _url)
            .after(((_title.length>0) ? _title : _url));
        _output[_i] = _option.html(); _i++;
    });

    mytpl.feed_options_form.find('div.inputfeedoption').remove();
    mytpl.feed_options_form.find('form').prepend(_output.join(''));
    mytpl.feed_options_form.find(mytpl.feed_option_selector + ":first").attr('checked', true);

    feed_add_hide();
    mytpl.feed_options_form.show();
}

function feed_discover_callback(d)
{
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
    if(mytpl.new_feed_url.val() == "")
    {
        add_message("<?php echo $this->translation()->form_incomplete ?>");
        return null;
    }

    $.ajax
    ({
        type: "POST",
        url: "./feed/discover",
        dataType: "xml",
        data: { url: mytpl.new_feed_url.val() },
        beforeSend: function () { set_active_request(true); add_message(''); },
        complete: function ()   { set_active_request(false); },
        success: function (xml) { feed_discover_callback($(xml).find('data')); },
        error: function () { server_error(); }
    });
}

function feed_add_callback(d)
{
    feed_populate(d.find('feed'), false);
}

function feed_add(u)
{
    $.ajax
    ({
        type: "POST",
        url: "./feed/add",
        dataType: "xml",
        data: { url  : u ,
                blog : blog.current },
        beforeSend: function () { set_active_request(true); add_message(''); },
        complete: function ()   { set_active_request(false); },
        success: function (xml) { feed_add_callback($(xml).find('data')); },
        error: function () { server_error(); }
    });
}

function feed_list_callback(d)
{
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
        data: { blog: blog.current },
        beforeSend: function () { set_active_request(true); },
        complete: function ()   { set_active_request(false); 
                                  $(document).trigger('after_feed_list'); },
        success: function (xml) { feed_list_callback($(xml).find('data')); },
        error: function () { server_error(); }
    });
}

function feed_edit_show(b)
{
    mytpl.feed_list_ref[b].item.find('a.feededitlnk').hide();
    mytpl.feed_list_ref[b].item.find('a.feeddeletelnk').show();
    disable_submit(); // form have only one input, disable submit from this
    mytpl.feed_list_ref[b].form.show();
}

function feed_edit_hide(b)
{
    mytpl.feed_list_ref[b].form.hide();
    mytpl.feed_list_ref[b].item.find('a.feeddeletelnk').hide();
    mytpl.feed_list_ref[b].item.find('a.feededitlnk').show();
}

function feed_remove_from_list(b)
{
    mytpl.feed_list_ref[b].item.next('div.feeddeletemsg').remove();
    mytpl.feed_list_ref[b].item.remove();
    mytpl.feed_list_ref[b].form.remove();
    mytpl.feed_list_ref[b] = null;
    flash_message("<?php echo $this->translation()->deleted ?>");
}

function feed_delete_confirm_show(b)
{
    feed_edit_hide(b);
    mytpl.feed_list_ref[b].item.find('a.feededitlnk').hide();
    mytpl.feed_list_ref[b].item.after(mytpl.feed_delete_blank.clone().html());
}

function feed_delete_confirm_hide(b)
{
    mytpl.feed_list_ref[b].item.next('div.feeddeletemsg').remove();
    mytpl.feed_list_ref[b].item.find('a.feededitlnk').show();
}

function feed_delete_callback(d)
{
    feed_remove_from_list(d.find('result').text());
}

function feed_delete(f)
{
    $.ajax
    ({
        type: "POST",
        url: "./feed/delete",
        dataType: "xml",
        data: { blog: blog.current,
                feed: f },
        beforeSend: function() { set_active_request(true); },
        complete: function() { set_active_request(false); },
        success: function (xml) { feed_delete_callback($(xml).find('data')); },
        error: function () { server_error(); }
    });
}

function feed_update_callback(d)
{
    var _updated = null;

    if((_updated = d.find('updated'))!=undefined)
    {
        var _feed = _updated.find('feed').text();
        var _title = _updated.find('feed_title').text();
        mytpl.feed_list_ref[_feed].item.find('span.feeditemname').text(_title);
        flash_message("<?php echo $this->translation()->saved ?>");
        feed_edit_hide(_feed);
    }
}

function feed_update(f)
{
    var _up = 
    {
        feed       : f,
        blog       : blog.current,
        feed_title : mytpl.feed_list_ref[f].form.find("input[name='title']").val(),
    }

    $.ajax
    ({
        type: "POST",
        url: "./feed/update",
        dataType: "xml",
        data: _up,
        beforeSend: function() { set_active_request(true); },
        complete: function() { set_active_request(false); },
        success: function (xml) { feed_update_callback($(xml).find('data')); },
        error: function () { server_error(); }
    });
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
    $.ajax
    ({
        type: "POST",
        url: "/feed/position",
        dataType: "xml",
        data: { blog     : blog.current , 
                feed     : f , 
                position : p },
        beforeSend: function() { set_active_request(true); },
        complete: function() { set_active_request(false); },
        success: function (xml) { feed_position_callback($(xml).find('data')); },
        error: function () { server_error(); }
    });
}

function sortable_callback(feed)
{
    var _p = 1;

    mytpl.feed_list_area.find('.feeditem').each(function()
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
    mytpl.feed_list_area.sortable(
    { 
        stop: function(e, ui)
        {
            sortable_callback(ui.item.attr('feed'));
        },
        handle: "div.feeditemleft",
        distance: 10
    });
    mytpl.feed_list_area.disableSelection();
}

function on_blog_change()
{
    feed_add_hide();
    feed_list(); 
}

$(document).ready(function()
{
    mytpl =
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
        feed_list_ref        : Array()
    }; 
    
    mytpl.new_feed_button.click(function()
    {
        if(active_request==false) { feed_add_toggle(); }
    });

    mytpl.new_feed_url.keypress(function(e)
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            if(active_request==false) { feed_discover(); }
        }
    });

    mytpl.new_feed_submit.click(function()
    {
        if(active_request==false) { feed_discover(); }
    });

    function feed_item_getid(i)
    {
        return i.parent().parent().attr('feed')
    }

    mytpl.feed_list_area.find('a.feededitlnk').live('click', function()
    {
        feed_edit_show(feed_item_getid($(this)));
        return false;
    });

    mytpl.feed_list_area.find('a.feeddeletelnk').live('click', function()
    {
        feed_delete_confirm_show(feed_item_getid($(this)));
        return false;
    });

    function feed_update_getid(i)
    {
        return i.parent().parent().parent().attr('feed')
    }

    mytpl.feed_list_area.find("input[name='feedupdatebtn']").live('click', function()
    {
        feed_update(feed_update_getid($(this)));
        return false;
    });

    mytpl.feed_list_area.find("input[name='feedcancelbtn']").live('click', function()
    {
        feed_edit_hide(feed_update_getid($(this)));
        return false;
    });

    function feed_delete_getid(i)
    {
        return i.parent().parent().parent().prev('div.feeditem').attr('feed');
    }

    mytpl.feed_list_area.find("input[name='feeddeletebtn']").live('click', function()
    {
        feed_delete(feed_delete_getid($(this)));
        return false;
    });

    mytpl.feed_list_area.find("input[name='feednodelbtn']").live('click', function()
    {
        feed_delete_confirm_hide(feed_delete_getid($(this)));
        return false;
    });

    mytpl.feed_options_submit.live('click', function()
    {
        mytpl.new_feed_url.val(mytpl.feed_options_form
            .find(mytpl.feed_option_selector + ":checked").attr('url'));
        feed_discover();
    });

    feed_list();

    $(document).bind('blog_changed' , function(e)
    {
        on_blog_change();
    });

    $(document).bind('after_feed_list' , function(e)
    {
        feed_sortable_init();
    });
});
