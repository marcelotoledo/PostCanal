$(document).ready(function()
{
    /* defaults */

    var active_request = false;
    var current_blog = null;
    var feed_add_options = $("tr#feedaddoptions > td");
    var feed_option_blank = feed_add_options.find("div#feedoptionblank");
    var feed_list_area = $("div#feedlistarea");
    var feed_item_blank = feed_list_area.find("div.feeditem[feed='blank']");

    /* spinner */

    $.b_spinner
    ({
        image: "<?php B_Helper::img_src('spinner.gif') ?>",
        message: "... <?php echo $this->translation()->application_loading ?>"
    });

    function set_active_request(b)
    {
        ((active_request = b) == true) ? $.b_spinner_start() : $.b_spinner_stop();
    }

    /* error */

    function err()
    {
        alert("<?php echo $this->translation()->server_error ?>");
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

    function toggle_feed_add_form(s)
    {
        if(s == true)
        {
            $("#feedaddlnkdiv").hide();
            $("#feedaddformtable").show();
            $("#feedaddurlrow").show();
        }
        else
        {
            $("#feedaddlnkdiv").show();
            $("#feedaddformtable").hide();
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
            type: "POST",
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
                    $.b_dialog({ selector: "#feedaddform" });
                    $.b_dialog_hide();
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
            },
            success: function (xml)
            {
                d = $(xml).find('data');
                feed_populate(d.find('feeds').children());
            },
            error: function () { err(); }
        });
    }

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
        newtitle = prompt("<?php echo $this->translation()->feed_rename ?>", _f);
        feed_update(feed, 'feed_title', newtitle);
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

    /* EVENTS */

    $("#feedaddlnkdiv").click(function()
    {
        toggle_feed_add_form(true);
    });

    $("input[name='feedaddcancel']").click(function()
    {
        toggle_feed_add_form(false);
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
        feedaddform_submit();
    });

    $("select[name='bloglst']").change(function()
    {
        set_blog();
    });

    /* INIT */

    /* set blog */

    function set_blog()
    {
        <?php if(count($this->blogs) == 1) : ?>
        current_blog = $("#blogcur").val();
        <?php elseif(count($this->blogs) > 1) : ?>
        current_blog = $("select[name='bloglst'] > option:selected").val();
        <?php endif ?>

        feed_list();
        toggle_feed_add_form(false);
    }

    set_blog();

    /* feed list is sortable */

    function feed_list_area_sortable()
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

    feed_list_area_sortable();
});
