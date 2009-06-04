var mytpl = null;
var feed_import_stack = Array();


function on_blog_change()
{
    toggle_feed_add_form(false);
    toggle_feed_import_form(false);
    feed_list(); 
}

function form_message(m)
{
    (m=="") ? 
        mytpl.feed_add_msg.hide().find("td").html("") :
        mytpl.feed_add_msg.show().find("td").html(m) ;
}

function toggle_feed_add_form(s)
{
    if(s == true)
    {
        mytpl.feed_lnk_div.hide();
        mytpl.feed_add_form.show();
    }
    else
    {
        mytpl.feed_lnk_div.show();
        mytpl.feed_add_form.hide();
        mytpl.feed_add_url.val("");
        mytpl.feed_add_url_row.show();
        mytpl.feed_add_options.find(".feedoption").remove();
        form_message("");
    }
}

function feedaddform_options(feeds)
{
    mytpl.feed_add_url_row.hide();

    var _lscontent = "";
    var _data = null;
    var _opt = null;

    feeds.each(function()
    {
        _data =
        {
            url         : $(this).find('feed_url').text(),
            title       : $(this).find('feed_title').text(),
            description : $(this).find('feed_description').text()
        };

        _opt = mytpl.feed_option_blank.clone();
        _opt.find("input[name='feedaddoption']").attr('url', _data.url);
        _opt.find("div.feedoptiontitle").html((_data.title.length > 0) ? 
            _data.title + "<br/><small>" + _data.url + "</small>" :
            _data.url);

        _lscontent += "<div class=\"feedoption\">" + _opt.html() + "</div>\n";
    });

    mytpl.feed_add_options.append(_lscontent);
    mytpl.feed_add_options.find("input[name='feedaddoption']:first").attr('checked', true);
}

function feed_discover(url)
{
    $.ajax
    ({
        type: "GET",
        url: "<?php B_Helper::url('feed', 'discover') ?>",
        dataType: "xml",
        data: { url: url },
        beforeSend: function()
        {
            set_active_request(true);
            form_message("");
        },
        complete: function()
        {
            set_active_request(false);
        },
        success: function (xml)
        {
            var _d = $(xml).find('data');
            var _r = _d.find('results')

            if(_r.length > 0) _r = _r.children();

            if(_r.length == 1)
            {
                feed_add(_r.find('feed_url').text());
            }
            else if(_r.length >  1)
            {
                feedaddform_options(_r);
            }
            else
            {
                form_message("<?php echo $this->translation()->feed_not_found ?>");
            }
        },
        error: function () { server_error(); }
    });
}

function feed_add(url)
{
    $.ajax
    ({
        type: "POST",
        url: "<?php B_Helper::url('feed', 'add') ?>",
        dataType: "xml",
        data: { url: url, blog: blog.current },
        beforeSend: function()
        {
            set_active_request(true);
            form_message("");
        },
        complete: function()
        {
            set_active_request(false);
            toggle_feed_add_form(false);
        },
        success: function (xml)
        {
            var _d = $(xml).find('data');
            var _f = _d.find('feed').text();

            if(_f.length > 0)
            {
                feed_list();
            }
            else
            {
                server_error();
            }
        },
        error: function () { server_error(); }
    });
}

function feedaddform_submit()
{
    var _url = mytpl.feed_add_options.find("input[name='feedaddoption']:checked").attr('url');

    if(_url!="" && _url!=undefined)
    {
        feed_discover(_url);
    }
    else
    {
        if((_url = mytpl.feed_add_url.val()) != "")
        {
            feed_discover(_url);
        }
        else
        {
            form_message("<?php echo $this->translation()->blank_url ?>");
        }
    }
}

function toggle_feed_import_form(s)
{
    if(s==true)
    {
        mytpl.feed_lnk_div.hide();
        mytpl.feed_import_form.show();
    }
    else
    {
        mytpl.feed_import_form.hide();
        mytpl.feed_lnk_div.show();
    }
}

function feed_import_preview(title)
{
    mytpl.feed_list_area.find("ul").prepend("<li>" + title + "</li>");
}

function feed_import()
{
    var stack_item = null;

    if((stack_item = feed_import_stack.shift()))
    {
        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('feed', 'import') ?>",
            dataType: "xml",
            data: { url   : stack_item.url, 
                    title : stack_item.title, 
                    blog  : blog.current },
            complete: function()
            {
                $(document).trigger('feed_import');
                feed_import_preview(stack_item.title);
            }
        });
    }
    else
    {
        set_active_request(false);
        $(document).trigger('after_feed_import');
    }
}

function feed_import_init()
{
    /*<?php foreach($this->import as $i) : ?>**/
    feed_import_stack.push({ 'url'  : "<?php echo $i['url'] ?>", 
                             'title': "<?php echo $i['title'] ?>" });
    /*<?php endforeach ?>**/

    if(feed_import_stack.length > 0)
    {
        $(document).trigger('before_feed_import');
        set_active_request(true);
        mytpl.feed_list_area.prepend("<ul></ul>");
        $(document).trigger('feed_import');
    }
}

function feed_populate(feeds)
{
    mytpl.feed_list_area.html("");

    if(feeds.length==0) { return null; }

    var _lscontent = "";
    var _item = null;
    var _data = null;
    var _left = null;
    var _toggle = null;

    feeds.each(function()
    {
        _data =
        {
            feed    : $(this).find('feed').text(),
            ord     : $(this).find('ordering').text(),
            url     : $(this).find('feed_url').text(),
            title   : $(this).find('feed_title').text(),
            enabled : ($(this).find('enabled').text() == 1)
        };

        _item = mytpl.feed_item_blank.clone();

        _left = _item.find("div.feeditemleft")
        _left.find("div.feeditemtitle").html(_data.title);
        _left.find("div.feeditemurl").html(_data.url);
        _toggle = _item.find("div.feeditemright").find("a.feedtogglelnk")

        if(_data.enabled)
        {
            _toggle.text("<?php echo $this->translation()->disable ?>");
        }
        else
        {
            _toggle.text("<?php echo $this->translation()->enable ?>");
        }

        _lscontent += "<div class=\"feeditem" + ((_data.enabled) ? "" : " feeditemdisabled") + 
                      "\" feed=\"" + _data.feed + "\" ord=\"" + _data.ord + "\">" + 
                      _item.html() + "</div>\n";
    });

    mytpl.feed_list_area.html(_lscontent);

    /* add events */

    var _feed = null;

    mytpl.feed_list_area.find('.feeditem').each(function()
    {
        $(this).find('a.feedrenamelnk').click(function()
        {
            feed_rename_show($(this).parent().parent().attr('feed'));
            return false;
        });
        $(this).find('a.feedtogglelnk').click(function()
        {
            feed_toggle($(this).parent().parent().attr('feed'));
            return false;
        });
        $(this).find('a.feeddeletelnk').click(function()
        {
            feed_set((_feed = $(this).parent().parent().attr('feed')));
            if(confirm("<?php echo $this->translation()->are_you_sure ?>"))
            {   
                feed_delete(_feed);
            }
            else
            {
                feed_unset(_feed);
            }
            return false;
        });
    });
}

function feed_list()
{
    if(blog.current==undefined) return null;

    $.ajax
    ({
        type: "GET",
        url: "<?php B_Helper::url('feed', 'list') ?>",
        dataType: "xml",
        data: { blog: blog.current },
        beforeSend: function()
        {
            set_active_request(true);
        },
        complete: function()
        {
            set_active_request(false);
            $(document).trigger('after_feed_list');
        },
        success: function (xml)
        {
            var _d = $(xml).find('data');
            feed_populate(_d.find('feeds').children());
        },
        error: function () { server_error(); }
    });
}

function feed_position(feed, position)
{
    $.ajax
    ({
        type: "POST",
        url: "<?php B_Helper::url('feed', 'position') ?>",
        dataType: "xml",
        data: { blog     : blog.current , 
                feed     : feed, 
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
                feed_list();
            }
        },
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
        handle: "div.feeditemleft"
    });
    mytpl.feed_list_area.disableSelection();
}

function feed_set(feed)
{
    mytpl.feed_list_area.find("div.feeditem[feed='" + feed + "']")
        .find("div.feeditemleft").addClass('feeditemleftbold');
}

function feed_unset(feed)
{
    mytpl.feed_list_area.find("div.feeditem[feed='" + feed + "']")
        .find("div.feeditemleft").removeClass('feeditemleftbold');
}

function feed_rename_show(feed)
{
    feed_set(feed);
    var _f = $("div.feeditem[feed='" + feed + "']").find("div.feeditemtitle").text();
    var _n = null;
    if(_n = prompt("<?php echo $this->translation()->feed_rename ?>", _f))
    {
        feed_update(feed, 'feed_title', _n);
    }
    feed_unset(feed);
}

function feed_update_callback(feed, updated)
{
    var _i = mytpl.feed_list_area.find("div.feeditem[feed='" + feed + "']");
    var _f = null;
    if((_f = updated.find('feed_title')))
    {
        _i.find("div.feeditemtitle").text(_f.text());
    }
    feed_unset(feed);
}

function feed_update(feed, k, v)
{
    $.ajax
    ({
        type: "POST",
        url: "<?php B_Helper::url('feed', 'update') ?>",
        dataType: "xml",
        data: { feed             : feed,
                blog             : blog.current,
                feed_title       : ((k=='feed_title') ? v : null),
                feed_description : ((k=='feed_description') ? v : null) },
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
            var _u = null;
            if((_u = _d.find('updated')))
            {
                feed_update_callback(feed, _u);
            }
        },
        error: function () { server_error(); }
    });
}

function feed_remove_from_list(feed)
{
    mytpl.feed_list_area.find("div.feeditem[feed='" + feed + "']").remove();
}

function feed_delete(feed)
{
    $.ajax
    ({
        type: "POST",
        url: "<?php B_Helper::url('feed', 'delete') ?>",
        dataType: "xml",
        data: { blog: blog.current, 
                feed: feed },
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
            feed_remove_from_list(_d.find('result').text());
        },
        error: function () { server_error(); }
    });
}

function feed_toggle_callback(feed, _e)
{
    var _i = mytpl.feed_list_area.find("div.feeditem[feed='" + feed + "']");
    var _t = _i.find("div.feeditemright").find("a.feedtogglelnk");

    if(_e == 1)
    {
        _i.removeClass('feeditemdisabled');
        _t.text("<?php echo $this->translation()->disable ?>");
    }
    else
    {
        _i.addClass('feeditemdisabled');
        _t.text("<?php echo $this->translation()->enable ?>");
    }
}

function feed_toggle(feed)
{
    var _i = mytpl.feed_list_area.find("div.feeditem[feed='" + feed + "']");
    var _e = _i.hasClass('feeditemdisabled') ? 1 : 0;

    $.ajax
    ({
        type: "POST",
        url: "<?php B_Helper::url('feed', 'toggle') ?>",
        dataType: "xml",
        data: { blog   : blog.current, 
                feed   : feed, 
                enable : _e },
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
            feed_toggle_callback(_d.find('result').text(), _e);
        },
        error: function () { server_error(); }
    });
}


$(document).ready(function()
{
    mytpl =
    {
        blog_list             : $("#bloglstsel"),
        feed_lnk_div          : $("#feedlnkdiv"),
        feed_add_form         : $("#feedaddform"),
        feed_add_options      : $("#feedaddoptions").find("td"),
        feed_add_option_blank : $("#feedaddoptionblank"),
        feed_add_lnk          : $("#feedaddlnk"),
        feed_add_cancel       : $("#feedaddcancel"),
        feed_add_submit       : $("#feedaddsubmit"),
        feed_add_url          : $("#feedaddurl"),
        feed_add_url_row      : $("#feedaddurlrow"),
        feed_add_msg          : $("#feedaddmessage"),
        feed_option_blank     : $("#feedoptionblank"),
        feed_list_area        : $("#feedlistarea"),
        feed_item_blank       : $("#feeditemblank"),
        feed_import_form      : $("#feedimportform"),
        feed_import_input     : $("#feedimportfeedinput"),
        feed_import_lnk       : $("#feedimportlnk"),
        feed_import_cancel    : $("#feedimportcancel"),
        feed_import_submit    : $("#feedimportsubmit")
    };

    /* triggers */

    mytpl.feed_add_lnk.click(function()
    {
        if(active_request == false)
        {
            toggle_feed_add_form(true);
        }
        return false;
    });

    mytpl.feed_add_cancel.click(function()
    {
        if(active_request == false)
        {
            toggle_feed_add_form(false);
        }
    });

    mytpl.feed_add_url.keypress(function(e)
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            mytpl.feed_add_submit.click();
        }
    });

    mytpl.feed_add_submit.click(function()
    {
        if(active_request == false)
        {
            feedaddform_submit();
        }
    });

    mytpl.feed_import_lnk.click(function()
    {
        if(active_request == false)
        {
            toggle_feed_import_form(true);
        }
        return false;
    });

    mytpl.feed_import_cancel.click(function()
    {
        if(active_request == false)
        {
            toggle_feed_import_form(false);
        }
    });

    // not working...
    // mytpl.feed_import_input.change(function(e)
    // {
    //     mytpl.feed_import_form.submit();
    // });

    /*<?php if(count($this->import) > 0) : ?>**/

    // not working...
    // $(document).bind('before_feed_import' , function(e)
    // { 
    //     mylyt.blog_list.attr('disabled', true);
    // });

    $(document).bind('feed_import' , function(e)
    { 
        feed_import();
    });

    $(document).bind('after_feed_import' , function(e)
    { 
        window.location="<?php B_Helper::url('feed') ?>" 
    });

    feed_import_init();

    /*<?php else : ?>**/

    /*<?php if(count($this->blogs)==0) : ?>**/

    $.b_dialog({ selector: "#noblogmsg", modal: false });
    $.b_dialog_show();

    /*<?php endif ?>**/

    feed_list();

    $(document).bind('blog_changed' , function(e)
    {
        on_blog_change();
    });

    $(document).bind('after_feed_list' , function(e)
    {
        feed_sortable_init();
    });

    /*<?php endif ?>**/
});
