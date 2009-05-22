$(document).ready(function()
{
    /* defaults */

    var active_request = false;

    var body__ = $("body");

    var blog_select_list = $("select[name='bloglst']");
    var current_blog = null;

    var feed_add_options = $("tr#feedaddoptions > td");
    var feed_option_blank = feed_add_options.find("div#feedoptionblank");
    var feed_list_area = $("div#feedlistarea");
    var feed_item_blank = feed_list_area.find("div.feeditem[feed='blank']");
    var feed_import_stack = Array();

    /* spinner */

    $.b_spinner
    ({
        image: "<?php B_Helper::img_src('spinner.gif') ?>",
        message: "... <?php echo $this->translation()->application_loading ?>"
    });

    function set_active_request(b)
    {
        if(((active_request = b) == true))
        {
            blog_select_list.attr('disabled', true);
            $.b_spinner_start();
        }
        else
        {
            blog_select_list.removeAttr('disabled');
            $.b_spinner_stop();
        }
    }

    /* error */

    function err()
    {
        alert("<?php echo $this->translation()->server_error ?>");
    }


    /* preference */

    function load_preference()
    {
        $.ajax
        ({
            type: "GET",
            url: "<?php B_Helper::url('profile', 'preference') ?>",
            dataType: "xml",
            data: { },
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
                d = $(xml).find('data');
                p = d.find('preference');
                b = p.find('current_blog').text()
                b = b ? b : current_selected_blog();
                set_blog(b);
            },
            error: function () { err(); }
        });
    }

    function save_preference(k, v)
    {
        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('profile', 'preference') ?>",
            dataType: "xml",
            data: { k: k, v: v },
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
                d = $(xml).find('data');
                k = d.find('k').text();
                v = d.find('v').text();

                if(k=='current_blog') { set_blog(v); }
            },
            error: function () { err(); }
        });
    }


    /* set blog */

    function current_selected_blog()
    {
        s = $("#blogcur").val();
        s = s ? s : blog_select_list.find("option:selected").val();
        return s;
    }

    function set_blog(b)
    {
        if((current_blog = b))
        {
            toggle_feed_add_form(false);
            toggle_feed_import_form(false);
            body__.trigger('after_blog');
        }
    }

    /* feed actions */

    function feed_msg(m)
    {
        _f  = $("#feedaddmessage");
        _td = $("#feedaddmessage td");

        if(m=="")
        {
            _td.html("");
            _f.hide();
        }
        else
        {
            _td.html(m);
            _f.show();
        }
    }

    /* feed add */

    function toggle_feed_add_form(s)
    {
        if(s == true)
        {
            $("#feedlnkdiv").hide();
            $("form#feedaddform").show();
            $("#feedaddurlrow").show();
        }
        else
        {
            $("#feedlnkdiv").show();
            $("form#feedaddform").hide();
            $("input[name='feedaddurl']").val("");
            $("#feedaddoptions > td").html("");
            feed_msg("");
        }
    }

    function feedaddform_options(feeds)
    {
        $("#feedaddurlrow").hide();

        feeds.each(function()
        {
            _url = $(this).find('feed_url').text();
            _title = $(this).find('feed_title').text();
            _description = $(this).find('feed_description').text();

            _div = feed_option_blank.clone(true);
            _div.find("input[name='feedaddoption']").attr('url', _url);
            _div.find("div.feedoptiontitle").html((_title.length > 0) ? 
                _title + "<br/><small>" + _url + "</small>" :
                _url);

            feed_add_options.append(_div);
        });

        $("input[name='feedaddoption']:first").attr('checked', 'checked');
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
                feed_msg("");
            },
            complete: function()
            {
                set_active_request(false);
            },
            success: function (xml)
            {
                d = $(xml).find('data');
                r = d.find('results')

                if(r.length > 0) r = r.children();

                if(r.length == 1)
                {
                    feed_add(r.find('feed_url').text());
                }
                else if(r.length >  1)
                {
                    feedaddform_options(r);
                }
                else
                {
                    feed_msg("<?php echo $this->translation()->feed_not_found ?>");
                }
            },
            error: function () { err(); }
        });
    }

    function feed_add(url)
    {
        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('feed', 'add') ?>",
            dataType: "xml",
            data: { url: url, blog: current_blog },
            beforeSend: function()
            {
                set_active_request(true);
                feed_msg("");
            },
            complete: function()
            {
                set_active_request(false);
                toggle_feed_add_form(false);
            },
            success: function (xml)
            {
                d = $(xml).find('data');
                f = d.find('feed').text();

                if(f.length > 0)
                {
                    feed_list();
                    current_feed = f;
                }
                else
                {
                    err();
                }
            },
            error: function () { err(); }
        });
    }

    function feedaddform_submit()
    {
        if((url = $("input[name='feedaddoption']:checked").attr('url')) != undefined)
        {
            feed_discover(url);
        }
        else
        {
            if((url = $("input[name='feedaddurl']").val()) != "")
            {
                feed_discover(url);
            }
            else
            {
                feed_msg("<?php echo $this->translation()->blank_url ?>");
            }
        }
    }

    /* feed import */

    function toggle_feed_import_form(s)
    {
        if(s == true)
        {
            $("#feedlnkdiv").hide();
            $("form#feedimportform").show();
        }
        else
        {
            $("form#feedimportform").hide();
            $("#feedlnkdiv").show();
        }
    }

    function feed_import_preview(title, added)
    {
        _st = added ? "<?php echo $this->translation()->added ?>" : 
                      "<?php echo $this->translation()->failed ?>";
        feed_list_area.prepend("- <i>" + title + " " + _st + "</i><br/>");
    }

    function feed_import()
    {
        if((stack_item = feed_import_stack.shift()))
        {
            set_active_request(true);
            $.ajax
            ({
                type: "POST",
                url: "<?php B_Helper::url('feed', 'import') ?>",
                dataType: "xml",
                data: { url   : stack_item.url, 
                        title : stack_item.title, 
                        blog  : current_blog },
                complete: function()
                {
                    feed_import();
                },
                success: function (xml)
                {
                    d = $(xml).find('data');
                    a = (d.find('added').text() == "true");
                    feed_import_preview(stack_item.title, a);
                }
            });
        }
        else
        {
            set_active_request(false);
            body__.trigger('after_import');
        }
    }

    function feed_import_init()
    {
        <?php foreach($this->import as $i): ?>
        feed_import_stack.push({ 'url'  : "<?php echo $i['url'] ?>", 
                                 'title': "<?php echo $i['title'] ?>" });
        <?php endforeach ?>

        if(feed_import_stack.length > 0)
        {
            feed_import();
        }
    }

    function feed_populate(feeds)
    {
        if(feeds.length > 0)
        {
            feed_list_area.html("");
            feeds.each(function()
            {
                _feed = $(this).find('feed').text();
                _ord = $(this).find('ordering').text();
                _url = $(this).find('feed_url').text();
                _title = $(this).find('feed_title').text();
                _enabled = ($(this).find('enabled').text() == 1);

                _div = feed_item_blank.clone();
                _div.attr('feed', _feed);
                _div.attr('ord', _ord);
                _l = _div.find("div.feeditemleft");
                _l.find("div.feeditemtitle").html(_title);
                _l.find("div.feeditemurl").html(_url);
                _div.find("div.feeditemright").find("a.feedrenamelnk").attr('feed', _feed);
                _t = _div.find("div.feeditemright > a.feedtogglelnk");
                _t.attr('feed', _feed);
                _div.find("div.feeditemright").find("a.feeddeletelnk").attr('feed', _feed);

                if(_enabled)
                {
                    _t.text("<?php echo $this->translation()->disable ?>");
                }
                else
                {
                    _t.text("<?php echo $this->translation()->enable ?>");
                    _div.addClass('feeditemdisabled');
                }

                feed_list_area.append(_div);
                _div.show();
            });
        }
        else
        {
            feed_list_area.html("<p><?php echo $this->translation()->no_registered_feeds ?></p>");
        }

        /* add events */

        feed_list_area.find("a.feedrenamelnk").click(function()
        {
            feed_rename_show($(this).attr('feed'));
        });

        feed_list_area.find("a.feedtogglelnk").click(function()
        {
            feed_toggle($(this).attr('feed'));
        });

        feed_list_area.find("a.feeddeletelnk").click(function()
        {
            feed = $(this).attr('feed');
            feed_set(feed);
            if(confirm("<?php echo $this->translation()->are_you_sure ?>"))
            {   
                feed_delete(feed);
            }
            else
            {
                feed_unset(feed);
            }
        });
    }

    function feed_list()
    {
        $.ajax
        ({
            type: "GET",
            url: "<?php B_Helper::url('feed', 'list') ?>",
            dataType: "xml",
            data: { blog: current_blog },
            beforeSend: function()
            {
                set_active_request(true);
            },
            complete: function()
            {
                set_active_request(false);
                body__.trigger('after_list');
            },
            success: function (xml)
            {
                d = $(xml).find('data');
                feed_populate(d.find('feeds').children());
            },
            error: function () { err(); }
        });
    }

    /* ordering */

    function feed_position(feed, position)
    {
        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('feed', 'position') ?>",
            dataType: "xml",
            data: { blog: current_blog, feed: feed, position: position },
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
                d = $(xml).find('data');
                u = (d.find('updated').text() == "true");

                if(u != true)
                {
                    feed_list();
                }
            },
            error: function () { err(); }
        });
    }

    function sortable_callback(feed)
    {
        var __p = 1;

        feed_list_area.find('.feeditem').each(function()
        {
            if(feed == $(this).attr('feed') && __p != $(this).attr('ord'))
            {
                feed_position(feed, __p);
            }

            __p++;
        });
    }

    function feed_sortable_init()
    {
        feed_list_area.sortable(
        { 
            stop: function(e, ui)
            {
                sortable_callback(ui.item.attr('feed'));
            },
            handle: "div.feeditemleft"
        });
        feed_list_area.disableSelection();
    }

    function feed_set(feed)
    {
        $("div.feeditem[feed='" + feed + "'] > div.feeditemleft").addClass('feeditemleftbold');
    }

    function feed_unset(feed)
    {
        $("div.feeditem[feed='" + feed + "'] > div.feeditemleft").removeClass('feeditemleftbold');
    }

    function feed_rename_show(feed)
    {
        feed_set(feed);
        _i = $("div.feeditem[feed='" + feed + "']");
        _f = _i.find("div.feeditemtitle").text();
        if(newtitle = prompt("<?php echo $this->translation()->feed_rename ?>", _f))
        {
            feed_update(feed, 'feed_title', newtitle);
        }
        else
        {
            feed_unset(feed);
        }
    }

    function feed_update_callback(result)
    {
        feed       = result.find('feed').text();
        _i = $("div.feeditem[feed='" + feed + "']");

        if((_f = result.find('feed_title')))
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
            data: { feed : feed,
                    blog : current_blog,
                    k    : k, 
                    v    : v },
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
                d = $(xml).find('data');
                if((r = d.find('result')))
                {
                    feed_update_callback(r);
                }
            },
            error: function () { err(); }
        });
    }

    function feed_remove_from_list(feed)
    {
        if(i = $("div.feeditem[feed='" + feed + "']"))
        {
            i.remove();
        }
    }

    function feed_delete(feed)
    {
        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('feed', 'delete') ?>",
            dataType: "xml",
            data: { blog: current_blog, feed: feed },
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
                d = $(xml).find('data');
                feed_remove_from_list(d.find('result').text());
            },
            error: function () { err(); }
        });
    }

    function feed_toggle_callback(feed, _e)
    {
        _i = $("div.feeditem[feed='" + feed + "']");
        _t = _i.find("div.feeditemright > a.feedtogglelnk");

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
        _i = $("div.feeditem[feed='" + feed + "']");
        _e = _i.hasClass('feeditemdisabled') ? 1 : 0;

        $.ajax
        ({
            type: "POST",
            url: "<?php B_Helper::url('feed', 'toggle') ?>",
            dataType: "xml",
            data: { blog: current_blog, feed: feed, enable: _e },
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
                d = $(xml).find('data');
                feed_toggle_callback(d.find('result').text(), _e);
            },
            error: function () { err(); }
        });
    }

    function main()
    {
        <?php if(count($this->blogs) > 0) : ?>
        load_preference();
        <?php else : ?>
        $.b_dialog({ selector: "#noblogmsg", modal: false });
        $.b_dialog_show();
        <?php endif ?>
    }

    /* TRIGGERS */

    $("a#feedaddlnk").click(function()
    {
        if(active_request == false)
        {
            toggle_feed_add_form(true);
        }
    });

    $("input[name='feedaddcancel']").click(function()
    {
        if(active_request == false)
        {
            toggle_feed_add_form(false);
        }
    });

    $("input[name='feedaddurl']").keypress(function(e)
    {
        if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13))
        {
            $("input[name='feedaddsubmit']").click();
        }
    });

    $("input[name='feedaddsubmit']").click(function()
    {
        if(active_request == false)
        {
            feedaddform_submit();
        }
    });

    $("a#feedimportlnk").click(function()
    {
        if(active_request == false)
        {
            toggle_feed_import_form(true);
        }
    });

    $("input[name='feedimportcancel']").click(function()
    {
        if(active_request == false)
        {
            toggle_feed_import_form(false);
        }
    });

    $("input[name='feedimportfile']").change(function(e)
    {
        $("input[name='feedimportsubmit']").click();
    });

    blog_select_list.change(function()
    {
        save_preference('current_blog', $(this).find("option:selected").val());
    });

    <?php if(count($this->import) > 0) : ?>

    body__.bind('main'          , function(e) { main(); });
    body__.bind('after_blog'    , function(e) { feed_import_init(); });
    body__.bind('after_import'  , function(e) { window.location="<?php B_Helper::url('feed') ?>" });
    body__.bind('after_list'    , function(e) { });

    <?php else : ?>

    body__.bind('main'          , function(e) { main(); });
    body__.bind('after_blog'    , function(e) { feed_list(); });
    body__.bind('after_list'    , function(e) { feed_sortable_init(); });
    body__.bind('after_import'  , function(e) { });

    <?php endif ?>

    body__.trigger('main');
});
